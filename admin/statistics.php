<?php

// include needed files
require('../config.php');
require('../global.php');
require("../lib/Pager.class.php");

// connect to the database
db_connect($mysql['username'], $mysql['password'], $mysql['database'],$mysql['host']);

// assign config options from database to an array
$config = get_config($mysql['prefix']);

debug_mode($config['debug_mode']);

// remove users that have not verified their email after 72 hours if email verification is enabled
if($config['verify_email']=='true' && $config['prune_inactive_users']=='true'){
	PruneInactiveUsers($mysql['prefix']);
}

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
$userdownloads = count_user_downloads($mysql['prefix'], $username);

// Find the start depending on $_GET['page'] (declared if it's null)
$start = $p->findStart($limit);

if (!empty($_GET['search']))
{
	$sql = 'SELECT * FROM `'.$mysql['prefix'].'users` WHERE CONCAT( `firstname`,`lastname`, `username` ) LIKE \'%'.mysql_escape_string($_GET['search']).'%\' and `status`=2';
	$sql2 = $sql.' LIMIT '.$start.', '.$limit;
}

else
{
	// list all users
	$sql = 'SELECT * FROM '.$mysql['prefix'].'users WHERE status=2 ORDER BY lastname';
	$sql2 = $sql.' LIMIT '.$start.', '.$limit;
}

if ($result = mysql_query($sql2))
{
	if (@mysql_num_rows($result) > 0)
	{
		$userlist = '';
		while (($row = mysql_fetch_array($result)) != false)
		{
			$userlist .= '<tr class="style2"><td>'.$row['lastname'].', '.$row['firstname'].'</td><td>'.$row['username'].'</td><td><b>'.count_user_downloads($mysql['prefix'], $row['username']).'</b> downloads <a href="./user_downloads.php?user='.$row['username'].'"><img src="images/mag.gif" alt="Total nubmer of user downloads" border="0" title="More Information" /></a> </td><td><b>'.count_user_logins($mysql['prefix'], $row['username']).'</b> logins <a href="./userlogins.php?loginusername='.$row['username'].'"><img src="images/time.gif" alt="Total number of user logins" border="0" title="View logins" /></a> </tr>'."\n";
		}
	}
	else
	{
		if (empty($_GET['search']))
		{
			$userlist = '<tr><td colspan="4"><span class="style11">There are currently no users.</span></td></tr>';
		}
		else
		{
			$userlist = '<tr><td colspan="4"><span class="style11">Your search returned 0 results.</span></td></tr>';
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
    <td height="28" colspan="2" class="style2"><div align="left"><br>
    This is a list of all users in the database. You can click a user's name to view more information about that user, to remove their account, or to send them an email. You may also enter a first name, last name OR username into the search box to find a user.</div></td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
        <form id="form1" name="form1" method="get" action="<?=$_SERVER['PHP_SELF']?>">
          <span class="style2">Search User:</span>
          <input type="text" name="search" />
          <input type="submit" value="Go" /><? if(!empty($_GET['search'])): ?><input type="button" value="View All" onclick="window.location='<?=$_SERVER['PHP_SELF']?>'" /><? endif; ?>
        </form>
<br /><br />
      <table width="100%" border="0">
        <tr>
          <td width="25%" class="style5">Name</td>
          <td width="25%" class="style5">Username</td>
          <td width="25%" class="style5">Downloaded Files</td>
          <td width="25%" class="style5">Access Time</td>
        </tr>
       <?=$userlist?>
      </table><br />
      <? if($count > 0): ?><div align="center"><span class="style2">Page:</span> <span class="style5"><?=$pagelist?></span></div><br /><? endif; ?>
    <br /></td>
  </tr>


</table>
  <?
include "admin_footer.php";
?>
