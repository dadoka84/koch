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

if(check_user_exists($_GET['user'],$mysql['prefix'])):

$result = mysql_query('SELECT * FROM '.$mysql['prefix'].'users WHERE username="'.$_GET['user'].'"');
while (($row = mysql_fetch_array($result)) != false) {
	$name = $row['firstname'].' '.$row['lastname'];
	$country = $row['country'];
	$phone = $row['phone'];
	$username = $row['username'];
	$email = $row['email'];
	$status = $row['status'];
	$RegistrationDate = date($config['date_format'],$row['registration_timestamp']);
}
if($country=='Not Selected'){
	$country = '<i>Not Available</i>';
}
if(empty($phone)){
	$phone = '<i>Not Available</i>';
}

switch($status){
	case '2':
	$statustext = '<font color="green">Active</font>';
	break;
	case '1':
	$statustext = '<font color="red">Inactive</font> - <i>Needs admin approval</i>';
	break;
	case '0':
	$statustext = '<font color="red">Inactive</font> - <i>Needs email verification</i>';
	break;
}
include "admin_header.php";
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="22" colspan="2" class="style2"><br />Information about a specific user can be found below.</td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
      <table width="70%" border="0">
      <tr>
        <td width="31%" class="style5">Full Name:</td>
        <td width="69%" class="style2"><?=$name?></td>
      </tr>
      <tr>
        <td class="style5">Username:</td>
        <td class="style2"><?=$username?></td>
      </tr>
      <tr>
        <td class="style5">Email Address: </td>
        <td class="style2"><? if($status=='2'): ?><a href="./bulkemail.php?user=<?=$username?>"><?=$email?></a><? else: print $email; endif; ?></td>
      </tr>
      <tr>
        <td class="style5">Country:</td>
        <td class="style2"><?=$country?></td>
      </tr>
      <tr>
        <td class="style5">Phone:</td>
        <td class="style2"><?=FormatPhoneNumber($phone)?></td>
      </tr>
      <tr>
        <td class="style5">Date Registered:</td>
        <td class="style2"><?=$RegistrationDate?></td>
      </tr>
      <tr>
        <td class="style5">Status:</td>
        <td class="style2"><?=$statustext?></td>
      </tr>
    </table>
      <div align="center"><br />
        <? if($status=='1'): ?>
        <input name="Button" type="button" value="Accept" onclick="acceptuser('<?=$username?>')" />
        <input name="Button" type="button" value="Decline" onclick="denyuser('<?=$username?>')" />
        <? else: ?>
        <input name="Button" type="button" value="Delete" onclick="deleteuser('<?=$username?>')" />
        <input type="submit" value="Edit" onclick="window.location='./edituser.php?user=<?=$username?>'" />
        <? endif; ?>
        <br />
        
              <span class="style2"><br />
              <br />
                <a href="./userlist.php">&lt;&lt; Back to user list</a><br/>
              <br/>
              <br/>
      </span></div></td>
  </tr>

</table>

<? 
include "admin_footer.php";
else: 
include "admin_header.php";
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td height="22" colspan="2" class="style2"><br />Information about a specific user is below. This page also shows the user's last 10 logins. </td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
      <span class="style2">Sorry, but the specified user was not found in the database. <br />
      <br /><a href="./userlist.php">&lt;&lt; Back to user list</a><br/><br/><br/>
      </span></td>
  </tr>
  
</table>

<? 
include "admin_footer.php";
endif; ?>