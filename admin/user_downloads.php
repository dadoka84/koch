<?php

// include needed files
require('../config.php');
require('../global.php');
require("../lib/Pager.class.php");
$user = $_GET['user'];
// connect to the database
db_connect($mysql['username'], $mysql['password'], $mysql['database'],$mysql['host']);

// assign config options from database to an array
$config = get_config($mysql['prefix']);

debug_mode($config['debug_mode']);


// start the session
admin_sessions($config['admin_session_expire']);
if (!isset($_SESSION['logged_in']))
{
	redirect('./login.php');
}

// start class
$p = new Pager;

// results per page
$limit = 30;

// Find the start depending on $_GET['page'] (declared if it's null)
$start = $p->findStart($limit);

{

$sql = 'SELECT * FROM deadlock_downloads WHERE `username` = \''.$user.'\'';

	$sql2 = $sql.' LIMIT '.$start.', '.$limit;
}

if ($result = mysql_query($sql2))
{
	if (@mysql_num_rows($result) > 0)
	{
		$stats = '';
		while (($row = mysql_fetch_array($result)) != false)
		{
			$stats .= '<tr class="style2"><td>'.$row['username'].'</td><td>'.date ('d M Y H:i',$row['timestamp']).'</td><td>'.$row['file'].'</td></tr>'."\n";
		}
	}
	
}

else
{
	die('The MySQL query failed. MySQL said: '.mysql_error());
}

$count = mysql_num_rows(mysql_query($sql));
$pages = $p->findPages($count, $limit);
// get the page list
$pagelist = $p->pageList($_GET['page'], $pages);
include "admin_header.php";
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td height="28" colspan="2" class="style2"><br />This is a list of all users in the database. You can click a user's name to view more information about that user, to remove their account, or to send them an email. You may also enter a first name, last name OR username into the search box to find a user.</td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
        
       
      <table width="500" border="0" align="center">
        <tr>
          <td width="30%" class="style5">Username</td>
          <td width="40%" class="style5">Date and Time</td>
          <td width="30%" class="style5">File Name</td>

        </tr>
       <?=$stats?>
      </table><br />
      <? if($count > 0): ?><div align="center"><span class="style2">Page:</span> <span class="style5"><?=$pagelist?></span><br />
        <br />
         <a href="./statistics.php">back to statistics page </a></div>
      <br /><? endif; ?>
    <br /></td>
  </tr>

</table>
  <?
include "admin_footer.php";
?>
