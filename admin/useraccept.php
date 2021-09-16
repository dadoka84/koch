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

if(isset($_GET['user'])){
	// get the user's current info
	if(check_user_exists($_GET['user'],$mysql['prefix'])){
		$sql = 'SELECT * FROM '.$mysql['prefix'].'users WHERE `username`=\''.$_GET['user'].'\'';
		if($result = mysql_query($sql)){
			while(($row = mysql_fetch_array($result)) != false){
				$username = $row['username'];
				$firstname = $row['firstname'];
				$lastname = $row['lastname'];
				$email = $row['email'];
				$phone = $row['phone'];
				$country = $row['country'];
				$username = $row['username'];
				$password = $row['password'];
			}
		} else {
			die('The following MySQL query failed. '.$sql);
		}
	}

	// check if the user exists, just to prevent errors. if they exist, remove them from the database
	if($_GET['action']=='deny'){
		if(check_user_exists($_GET['user'],$mysql['prefix'])){
			if(!remove_user($_GET['user'],$mysql['prefix'])){
				$error=1;
			} else {
				if($config['email_user_accept']=='true'){
					if(!sendmail($email,$config['admin_email'],get_email_subject($mysql['prefix'],'user_AccountDenied'),get_email_body($firstname,$lastname,$email,$username,$password,$config['protected_area_url'],$config['deadlock_url'],$config['admin_email'],$mysql['prefix'],'user_AccountDenied'))){
						die('Deadlock was unable to send an email to the user.');
					}
				}
			}
		}
	}

	// check if the user exists, if so, change their status to 2
	if($_GET['action']=='accept'){
		if(check_user_exists($_GET['user'],$mysql['prefix'])){
			if(!accept_user_request($_GET['user'],$mysql['prefix'])){
				$error=1;
			} else {
				if($config['email_user_accept']=='true'){
					if(!sendmail($email,$config['admin_email'],get_email_subject($mysql['prefix'],'user_AccountApproved'),get_email_body($firstname,$lastname,$email,$username,$password,$config['protected_area_url'],$config['deadlock_url'],$config['admin_email'],$mysql['prefix'],'user_AccountApproved'))){
						die('Deadlock was unable to send an email to the user.');
					}
				}
				generate_htpasswd($mysql['prefix']);
			}
		}
	}
}

if(!isset($error)):
include "admin_header.php";
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="28" colspan="2" class="style2"><br />The user &quot;<?=htmlentities($_GET['user'])?>&quot; was updated successfully! Please wait while your are redirected to the user request list.</td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
      <span class="style2">If you are not redirected within 5 seconds, <a href="./userrequests.php">click here</a>...</span><br /><br /><br /></td>
  </tr>

</table>

<? 
include "admin_footer.php";
else: 
include "admin_header.php";
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td height="28" colspan="2" class="style2"><br />The user <?=htmlentities($_GET['user'])?> was unable to be updated. Please make sure MySQL is running and setup correctly. Please wait while your are redirected to the request list.</td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
      <span class="style2">If you are not redirected within 5 seconds, <a href="./userrequests.php">click here</a>...</span><br /><br /><br /></td>
  </tr>
  
</table>

<?php 
include "admin_footer.php";
endif;
?>