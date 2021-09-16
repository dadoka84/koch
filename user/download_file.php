<?php

// include needed files
require('../config.php');
require('../global.php');

// connect to the database
db_connect($mysql['username'],$mysql['password'],$mysql['database'],$mysql['host']);

// assign config options from database to an array
$config = get_config($mysql['prefix']);

debug_mode($config['debug_mode']);

// remove users that have not verified their email after 72 hours if email verification is enabled
if($config['verify_email']=='true' && $config['prune_inactive_users']=='true'){
	PruneInactiveUsers($mysql['prefix']);
}

// make sure user is logged in
require('auth.inc.php');


if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['verify']))
{
	remove_user($_SESSION['username'],$mysql['prefix']);
	generate_htpasswd($mysql['prefix']);
	session_destroy();
	redirect('./login.php');
}
//start load time
if($load_time)
{
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
}
//Start load time -done

if(get_magic_quotes_gpc())
$folder = stripslashes($_GET['folder']);
else
$folder = $_GET['folder'];
$dir_to_browse_original = $dir_to_browse;
if(!empty($folder))
{
	$dir_to_browse = $dir_to_browse.$folder."/";
	$folder_slash = $folder."/";
}

include "../templates/overall_header.html";

//Check if directory is valid
$folder_exists = 1;
if(!is_dir($dir_to_browse)) 
{
	$folder = "";
	$folder_exists = 0;
}

if($folder_exists == 0)
{
	echo display_error_message("<b>Error:</b> Folder specified does not exist. This could be because you manually entered the folder name in the URL or you don't have permission to access this folder");
	exit;
}
//Chcek if directory is valid -done

//This is a VERY important security feature. It wont allow people to browse directories above $dir_to_browse. Edit this part at your own risk
$folders_in_URL_security = explode("../",$folder);
if(count($folders_in_URL_security) > 1)
{
	echo display_error_message("<b>Access Denied.</b>");
	exit;
}
//Seurity feature -done

//Create navigation links
$this_file_name_array = explode("/",$_SERVER['PHP_SELF']);
$this_file_name = array_pop($this_file_name_array);
$folders_in_URL = explode("/",rawurldecode($folder));
$depth = count($folders_in_URL);

foreach($folders_in_URL as $key => $val)
{
	$_temp = $_temp."/".$val;
	$folders_in_URL_array[$key] = $_temp;
	if($key == 0)
	{
		$_temp = $folders_in_URL[0];
		$folders_in_URL_array[0] = $folders_in_URL[0];
	}
}

$nav_links = "<a href=\"$this_file_name\">home</a>/";

for($i=0;$i<$depth;$i++)
{
	if(!empty($folder))
	$nav_links = $nav_links."<a href=\"$this_file_name?folder=".rawurlencode($folders_in_URL_array[$i])."\">$folders_in_URL[$i]</a>/";
	else
	$nav_links = $nav_links."<a href=\"$this_file_name?folder=".rawurlencode($folders_in_URL_array[$i])."\">$folders_in_URL[$i]</a>";
}

echo "<br><p align=\"center\">Index of: ".$nav_links."<br><br>";
//Create navigation links -done

//Get directory content (filtered(without the excluded specified above)) and seperate files and folders to 2 arrays
$dir_content = get_dir_content($dir_to_browse);

//If file extensions are NOT case sensative
if($case_sensative_ext == 0)
{
	foreach($exclude_ext as $key => $val)
	{
		$temp_ext = $val;
		$exclude_ext[$key] = strtolower($temp_ext);
	}
}
//If file extensions are NOT case sensative -done

//Filter content
$filtered_content = array();
$filter_count = 0;
foreach($dir_content as $key => $val)
{
	$file_ext = explode(".",$val);
	$file_ext = ".".array_pop($file_ext);
	if(!in_array($val, $exclude) && !in_array($file_ext,$exclude_ext))
	{
		$filtered_content[$filter_count] = $val;
		$filter_count++;
	}
}
if($exclude_this_file == 1)
{
	$array_with_this_file_name = array();
	$temp_filtered_content = $filtered_content;
	$filtered_content = array();
	foreach($temp_filtered_content as $key => $val)
	{
		if($val == $this_file_name)
		{
			array_push($array_with_this_file_name, $key);	
		}
		else
		$filtered_content[$key] = $val;	
	}
	foreach($array_with_this_file_name as $key => $val)
	{
		$temp_path = $folder."/".$this_file_name;
		if(substr($temp_path,0,1) == "/") $temp_path = substr($temp_path,1);
		if(is_file($temp_path))
		{
			$md5_temp_path = md5_file($temp_path);
			$md5_this_file = md5_file($_SERVER['SCRIPT_FILENAME']);
			if($md5_temp_path != $md5_this_file)
			{
			array_push($filtered_content, $this_file_name);
			}
		}
		else
		array_push($filtered_content, $this_file_name);
		
	}
}	
//Filter content -done

//Get directory content and seperate files and folders into 2 arrays -done

if(!empty($filtered_content))
{
	$folders_count = 0;
	$files_count = 0;
	
	$folders_array = array();
	$files_array = array();
		
	foreach($filtered_content as $key => $val)
	{
		$path = $dir_to_browse.$val;
		if(is_dir($path))
		{
			array_push($folders_array, $val);
		}
		else
		{
			array_push($files_array, $val);
		}
	}
	
	//Sort the files and folders arrays
		if(!empty($folders_array)) natcasesort($folders_array);
		if(!empty($files_array)) natcasesort($files_array);
		$folders_array_temp = array();
		$files_array_temp = array();
		foreach($folders_array as $key => $val)
		{
			array_push($folders_array_temp, $val);
		}
		foreach($files_array as $key => $val)
		{
			array_push($files_array_temp, $val);
		}
		$folders_array = $folders_array_temp;
		$files_array = $files_array_temp;
	//Sort the files and folders arrays -done
	
	//Determin OS PHP is running on
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
	$OS = "windows";
	//Determin OS PHP is running on -done
	
	//Get files and folders sizes and creation dates
		$folders_cdate_array = array();
		$files_cdate_array = array();
		$folders_size_array = array();
		$files_size_array = array();
		$files_total_size = 0;
		$folders_total_size = 0;
		
		foreach($files_array as $key => $val)
		{
			$path = $dir_to_browse.$val;
			$files_cdate_array[$key] = date("d F Y", filectime($path));
			$file_bytes = filesize($path);
			$files_size_array[$key] = letter_size($file_bytes);
			$files_total_size = $files_total_size + $file_bytes;
		}
		
		foreach($folders_array as $key => $val)
		{
			$path = $dir_to_browse.$val;
			$path_slash = $dir_to_browse.$val."/";
			$folders_cdate_array[$key] = date("d F Y", filectime($path));
			if($show_folder_size)
			{
				if($OS == "windows")
				$folder_bytes = folder_size_windows($path_slash);
				else
				$folder_bytes = folder_size($path);
				$folders_size_array[$key] = letter_size($folder_bytes);
				$folders_total_size = $folders_total_size + $folder_bytes;
			}
			else
			$folders_size_array[$key] = "-";
		}
	//Get files and folders sizes and creation dates -done
	
	//Generate content links to an array
		$folders_links_array = array();
		$files_links_array = array();
		
		foreach($folders_array as $key => $val)
		{
			$folders_links_array[$key] = '<a href="'.$this_file_name.'?folder='.rawurlencode($folder_slash).rawurlencode($val).'">'.$val."</a><br>";
		}
		
		foreach($files_array as $key => $val)
		{
			if(empty($folder))
			$files_links_array[$key] = '<a href=download.php?filename='.rawurlencode($val).'&filepath='.$dir_to_browse_original.'&username='.$_SESSION['username'].'>'.$val."</a><br>";
			else
			$files_links_array[$key] = '<a href=download.php?filepath='.$dir_to_browse_original.$folder.'/&filename='.rawurlencode($val).'&username='.$_SESSION['username'].'>'.$val."</a><br>";
		}
	
	//Generate content links to an array -done
	
	//Calculate table dimensions
	$table_width = (6*$cell_padding) + (4*$cell_spacing) + $width_of_files_column + $width_of_sizes_column + $width_of_dates_column;
	//calculate table dimensions -done
	
	//Palce the content into a table
	if($legend) echo "
<table align=\"center\" width=\"320\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\" class=\"table_border\">
  <tr>
    <td width=\"100\" bgcolor=\"#666666\"><font color=\"#FFFFFF\"><strong>KEY</strong></font></td>
    <td width=\"33\">Folder</td>
    <td width=\"24\"><table width=\"24\" height=\"24\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
      <tr>
        <td colspan=\"4\" bgcolor=\"$folder_color\">&nbsp;</td>
      </tr>
    </table></td>
    <td width=\"17\">File</td>
    <td width=\"24\"><table width=\"24\" height=\"4\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
      <tr>
        <td colspan=\"4\" bgcolor=\"$color_1\">&nbsp;</td>
      </tr>
    </table>     </td>
    <td width=\"5\">|</td>
    <td><table width=\"24\" height=\"24\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
      <tr>
        <td colspan=\"4\" bgcolor=\"$color_2\">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table><br>";
	
	if($statistics) 
	{
	echo'<table align=\"center\"><tr><td>
<a href="javascript:;" onMouseDown="if(document.getElementById(\'mydiv\').style.display == \'none\'){ document.getElementById(\'mydiv\').style.display = \'block\'; }else{ document.getElementById(\'mydiv\').style.display = \'none\'; }">Show/hide statistics<br></a>
	<div id="mydiv" style="display:none">
	<table width="320" border="0" cellpadding="5" class="table_border">';
	  if($show_folder_size)
	  echo'<tr>
		<td width="95" bgcolor="#666666"><font color="#FFFFFF"><strong>Folders count </strong></font></td>
		<td>'.count($folders_array).', consuming: '.letter_size($folders_total_size).'</td>
	  </tr>';
	  echo'
	  <tr>
		<td width="95" bgcolor="#666666"><strong><font color="#FFFFFF">Files count </font></strong></td>
		<td>'.count($files_array).', consuming: '.letter_size($files_total_size).'</td>
	  </tr>';
	  if($show_folder_size)
	  echo'<tr>
		<td width="95" bgcolor="#666666"><strong><font color="#FFFFFF">Total count </font></strong></td>
		<td>'.(count($folders_array)+count($files_array)).', consuming: '.letter_size(($files_total_size+$folders_total_size)).'</td>
	  </tr></table>';}
	echo"
	</table></div><br><table align=\"center\" width=\"$table_width\" border=\"0\" cellspacing=\"$cell_spacing\" cellpadding=\"$cell_padding\">
	  <tr>
		<td width=\"$width_of_files_column\" bgcolor=\"$top_row_bg_color\"><span class=\"top_row\">File</span></td>
		<td width=\"$width_of_sizes_column\" bgcolor=\"$top_row_bg_color\"><span class=\"top_row\">Size</span></td>
		<td width=\"$width_of_dates_column\" bgcolor=\"$top_row_bg_color\"><span class=\"top_row\">Date uploaded</span></td>
	  </tr>";
	 
	
	foreach($folders_links_array as $key => $val)
	{
		echo"<tr>
		<td width=\"$width_of_files_column\" bgcolor=\"$folder_color\">$val</td>
		<td width=\"$width_of_sizes_column\" bgcolor=\"$folder_color\">$folders_size_array[$key]</td>
		<td width=\"$width_of_dates_column\" bgcolor=\"$folder_color\">$folders_cdate_array[$key]</td></tr>";
	}
	foreach($files_links_array as $key => $val)
	{
		if($key%2 == 0) $color = $color_1; else $color = $color_2;
		echo"<tr>
		<td width=\"$width_of_files_column\" bgcolor=\"$color\">$val</td>
		<td width=\"$width_of_sizes_column\" bgcolor=\"$color\">$files_size_array[$key]</td>
		<td width=\"$width_of_dates_column\" bgcolor=\"$color\">$files_cdate_array[$key]</td></tr>";
	}
	echo "</table>";
	//Palce the content into a table -done
	
//Closing curley brace for the if(!empty($filtered_content))
}
//Get directory content (filtered(without the excluded specified above)) and seperate files and folders to 2 arrays -done

//Output if there is no content
if(empty($filtered_content)) 
echo display_error_message("No files or folders in this directory: <span class=\"path_font\"><b>$folder</b></span>");
//Output if there is no content -done

//Functions
function get_dir_content($path)
{
	$php_version = substr(phpversion(),0,1);
	if($php_version > 4)
	{
		$content = scandir($path);
		return $content;
	}
	elseif($php_version == 4)
	{
		$content = array();
		if ($handle = opendir($path)) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				array_push($content,$file);
			}
			closedir($handle);
		}
		return $content;
	}
}

function folder_size($path)
{
	$folder_size = `du -s $path`;
	$size_array = explode("\t", $folder_size);
	return $size_array[0]*1024;
}

function folder_size_windows($path)
{
	$exclude = array('.','..');
	$content = glob($path);
	
	//Filter content
	$filtered_content = array();
	$filtered_content = array();
	foreach($content as $key => $val)
	{
		if(!in_array($val, $exclude))
		{
			array_push($filtered_content, $val);
		}
	}
	//Filter content -done	
	
	foreach($filtered_content as $key => $val)
	{
		$content_path = $path.$val;
		if(is_dir($content_path))
		{
			
			$content_path = $path.$val."/";
			$directory_size = folder_size_windows($content_path);
			$total_directory_size = $total_directory_size + $directory_size ;
			
		}
		else
		{
			$file_size = filesize($content_path);
			$total_file_size = $total_file_size + $file_size;
		}
	}
	return ($total_file_size + $total_directory_size);
}

function letter_size($byte_size)
{
	$file_size = $byte_size/1024;
		if ($file_size >=  1048576)
	{
		$file_size = $file_size/1048576;
		$file_size = sprintf("%01.2f", $file_size);
		$file_size = $file_size." GB";
	}
	elseif ($file_size >=  1024)
	{
		$file_size = $file_size/1024;
		$file_size = sprintf("%01.2f", $file_size);
		$file_size = $file_size." MB";
	}
	else
	{
		$file_size = sprintf("%01.1f", $file_size);
		$file_size = $file_size." KB";	
	}
	return $file_size;
}

function display_error_message($message)
{
	return "<table align=\"center\" width=\"80%\" cellpadding=\"5\" cellspacing=\"1\" class=\"table_border\">
  <tr>
    <td bgcolor=\"#FFBBBD\">$message</td>
  </tr>
</table>";
}	
//Functions -done

//Display load time
if($load_time)
{
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$endtime = $mtime;
	$totaltime = ($endtime - $starttime);
	echo "<br><p align=\"center\">This page loaded in ".sprintf("%.3f", $totaltime)." seconds";
}
//Display load time -done


include "../templates/overall_footer.html";
?>