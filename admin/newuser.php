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

if(isset($_POST['submit'])){
	if(empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email']) || !validate_optional_fields($_POST['phone'], $config['optional_fields_phone']) || !validate_optional_fields($_POST['country'], $config['optional_fields_country']) || match_string($_POST['country'],'Not Selected',$config['optional_fields_country']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password2'])){
		$errors[] = 'One or more required fields were left empty. Please fill in all required fields.';
	} else {
		$_POST['firstname'] = ucwords(strtolower($_POST['firstname']));
		$_POST['lastname'] = ucwords(strtolower($_POST['lastname']));
		if(!validate_email_address($_POST['email'])){
			$errors[] = 'The email address you entered was invalid.';
		}
		if(!validate_name($_POST['firstname'])){
			$errors[] = 'Please enter a first name between 1 and 15 characters.';
		}
		if(!validate_name($_POST['lastname'])){
			$errors[] = 'Please enter a last name between 1 and 15 characters.';
		}
		if(!validate_username($_POST['username'])){
			$errors[] = 'The username must be alphanumeric and 5-15 characters long.';
		}
		if(check_email_exists($_POST['email'],$mysql['prefix'])){
			$errors[] = 'The email address you entered already exists for another user.';
		}
		if(strlen($_POST['email']) > 60){
			$errors[] = 'Please enter an email that is no longer than 60 characters.';
		}
		if($_POST['password'] != $_POST['password2']){
			$errors[] = 'The passwords you entered did not match.';
		} else {
			if(!validate_password($_POST['password'])){
				$errors[] = 'For maximum security, your password must be between 6 and 10 characters long, and it must contain at least one letter and one number.';
			}
		}
		if(!validate_phone($_POST['phone'],$config['phone_digits'],$config['optional_fields_phone'])){
			$errors[] = 'Your phone number must be numeric and contain '.$config['phone_digits'].' digits.';
		}
		if(check_user_exists($_POST['username'],$mysql['prefix'])){
			$errors[] = 'The username you have selected is already taken. Please choose a new one.';
		}
	}
	if(empty($errors)){
		if(!add_dbuser($_POST['firstname'],$_POST['lastname'],$_POST['email'],$_POST['phone'],$_POST['country'],$_POST['username'],$_POST['password'],$mysql['prefix'],true,true)){
			die('There was an error inserting data into the database.');
		}
		generate_htpasswd($mysql['prefix']);
		$welcome_email_path = '../emails/UserWelcome_user.txt';
		$notify_admin_email_path = '../emails/AdminNotify_admin.txt';
		if(isset($_POST['welcome_user'])){
			if(!sendmail($_POST['email'],$config['admin_email'],get_email_subject($mysql['prefix'],'user_WelcomeEmail'),get_email_body($_POST['firstname'],$_POST['lastname'],$_POST['email'],$_POST['username'],$_POST['password'],$config['protected_area_url'],$config['deadlock_url'],$config['admin_email'],$mysql['prefix'],'user_WelcomeEmail'))){
				die('There was an error sending a welcome email to the new user.');
			}
		}
		if(isset($_POST['notify_admin'])){
			if(!sendmail($config['admin_email'],$config['system_messages_email'],get_email_subject($mysql['prefix'],'admin_NewUser'),get_email_body($_POST['firstname'],$_POST['lastname'],$_POST['email'],$_POST['username'],$_POST['password'],$config['protected_area_url'],$config['deadlock_url'],$config['admin_email'],$mysql['prefix'],'admin_NewUser'))){
				die('There was an error sending a notification email to the administrator.');
			}
		}
		// Everything was successful at this point. Print out the success page.
include "admin_header.php";
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td height="28" colspan="2" class="style2"><br>The user <?=htmlentities($_POST['username'])?> was created successfully! Please wait while your are redirected to the user's profile.</td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br />
      <span class="style2">If you are not redirected within 5 seconds, <a href="./userinfo.php?user=<?=htmlentities($_POST['username'])?>">click here</a>...</span><br /><br /><br /></td>
  </tr>
  

</table>

<?php
include "admin_footer.php";
exit;
	}
}
include "admin_header.php";
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td height="28" colspan="2" class="style2"><br>To add a user, fill in the required fields below. A welcome email will be sent to the email address provided once the account has been created. </td>
  </tr>
  <tr>
    <td height="275" colspan="2"><? if (!empty($errors)){ ?><table width="95%" height="24" border="0" align="center">
      <tr>
        <td height="20">
		<div class="style9"><ul>
		<?php
		foreach($errors as $error){
			print '<li>'.$error.'</li>';
		}
		?>
		</ul></div></td>
      </tr>
    </table>
      <? } else { print '<br />'; } ?>
	  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
      <table width="73%" border="0" align="center">
      <tr>
        <td width="47%" class="style5">First Name:</td>
        <td width="53%"><input name="firstname" maxlength="15" type="text" id="firstname"<? if(isset($_POST['firstname'])) print ' value="'.$_POST['firstname'].'"'; ?> /><a href="#" class="hintanchor" onMouseover="showhint('Please enter your first name. This must be 1-15 characters and alphanumeric.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style5">Last Name: </td>
        <td><input name="lastname" maxlength="15" type="text" id="lastname"<? if(isset($_POST['lastname'])) print ' value="'.$_POST['lastname'].'"'; ?> /><a href="#" class="hintanchor" onMouseover="showhint('Please enter your last name. This must be 1-15 characters and alphanumeric.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style5">Email:</td>
        <td><input name="email" type="text" id="email"<? if(isset($_POST['email'])) print ' value="'.$_POST['email'].'"'; ?> /><a href="#" class="hintanchor" onMouseover="showhint('Please enter your email address. This email address must be valid.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style5">Phone<? if($config['optional_fields_phone']=="false") print ' (optional)'; ?>: </td>
        <td><input name="phone" type="text" id="phone"<? if(isset($_POST['phone'])) print ' value="'.$_POST['phone'].'"'; ?> /><a href="#" class="hintanchor" onMouseover="showhint('Please enter your phone number. This should be <?=$config['phone_digits']?> digits and contain only numbers.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style5">Country<? if($config['optional_fields_country']=="false") print ' (optional)'; ?>:</td>
        <td>
<?php
if(isset($_POST['country'])){
	print country_menu($_POST['country']);
} else {
	print country_menu('Not Selected');
}
?></td>
      </tr>
      <tr class="style5">
        <td colspan="2" class="style2">&nbsp;</td>
        </tr>
      <tr>
        <td class="style5">Username:</td>
        <td><input name="username" maxlength="15" type="text" id="username"<? if(isset($_POST['username'])) print ' value="'.$_POST['username'].'"'; ?> /><a href="#" class="hintanchor" onMouseover="showhint('Please choose a username. This must be alphanumeric and contain 5-10 characters.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style5">Password:</td>
        <td><input name="password" maxlength="10" type="password" id="password" /><a href="#" class="hintanchor" onMouseover="showhint('Please choose a password. For maximum security this must be 6-10 characters and must contain at least one letter and one number', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style5">Confirm Password: </td>
        <td><input name="password2" maxlength="10" type="password" id="password2" /><a href="#" class="hintanchor" onMouseover="showhint('Please confirm the password you entered above.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td class="style2"><input name="welcome_user" type="checkbox" id="welcome_user" value="1" <? if(($config['user_welcome_email']=="true" && !isset($_POST['submit'])) || isset($_POST['welcome_user'])) print 'checked="checked" '; ?>/>
        Send welcome email<a href="#" class="hintanchor2" onMouseover="showhint('Check this box if you would like to send a welcome email to the above email address.', this, event, '150px')">[?]</a></td>
        <td class="style2"><input name="notify_admin" type="checkbox" id="notify_admin" value="checkbox" <? if(($config['admin_user_email']=="true" && !isset($_POST['submit'])) || isset($_POST['notify_admin'])) print 'checked="checked" '; ?>/>
Notify administrator<a href="#" class="hintanchor2" onmouseover="showhint('Check this box if you would like to send the administrator a notification email with this user\'s information.', this, event, '150px')">[?]</a></td>
      </tr>
      <tr>
        <td colspan="2" class="style2">&nbsp;</td>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="hidden" name="submit" value="1" /><input type="submit" value="Create User Account" /></td>
      </tr>
    </table>
	<br />
	</form>    </td>
  </tr>

</table>
<?
include "admin_footer.php";
?>