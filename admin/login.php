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

// set the session name so that there is no conflict
session_name('admin_sid');

// start the session
session_start();

// if the query string says to logout, remove the session
if(isset($_GET['cmd']) && $_GET['cmd'] == 'logout' && isset($_SESSION['logged_in'])){
	session_destroy();
	redirect($_SERVER['PHP_SELF']);
}

// if the admin is already logged in, redirect to index.php
if(isset($_SESSION['logged_in'])){
	redirect('./index.php');
}



if(isset($_POST['submit'])){
	$numfailed = CheckFailedLogins($mysql['prefix'],$_SERVER['REMOTE_ADDR']);
	if($numfailed >= 5){
		$message = 'You have reached the maximum number of failed login attempts (5). Please wait 10 minutes and try again.';
	} else {
		if($_POST['password'] == $config['admin_pass']){
			$_SESSION['logged_in'] = 1;
			redirect('index.php');
		} else {
			LogFailedLogin($mysql['prefix'],'admin');
			$numfailed = CheckFailedLogins($mysql['prefix'],$_SERVER['REMOTE_ADDR']);
			$numleft = 5 - $numfailed;
			$message = 'The password you entered was incorrect. All failed logins are logged. You have '.$numleft.' login attempts left.';
		}
	}
} else {
	$message = 'Welcome to the administration panel. Please enter your password below, then click &quot;Login&quot; to login to access this area. Please note that all failed attemps are logged in the database. After 5 failed logins, you will not be able to login to the panel for 10 minutes.';
}
include "admin_header_login.php";
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td height="22" colspan="2" class="style2"><br><?=$message?></td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br /><br/><div align="center">
      <form id="form1" name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
        <span class="style2">Password:</span>
        <input name="password" type="password" id="password" />
        <input type="submit" value="Login" />
        <input name="submit" type="hidden" id="submit" value="1" />
      </form>
    </div><br/><br/><br/></td>
  </tr>
</table>
<?
include "admin_footer.php";
?>