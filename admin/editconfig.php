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

// the form has been submitted, start error checking
if(isset($_POST['submit'])){
	if(empty($_POST['admin_email'])){
		$errors[] = 'You must enter an administrator email address (Admin Email).';
	}
	if(empty($_POST['system_email'])){
		$errors[] = 'You must enter a system email (System Email).';
	}
	if(!empty($_POST['admin_email']) && !validate_email_address($_POST['admin_email'])){
		$errors[] = 'The admin email address you entered is invalid.';
	}
	if(!empty($_POST['system_email']) && !validate_email_address($_POST['system_email'])){
		$errors[] = 'The system email address you entered is invalid.';
	}
	if(empty($_POST['phone_digits'])){
		$errors[] = 'Please specify the number of digits required in a phone number (Phone Digits).';
	} elseif(!is_numeric($_POST['phone_digits'])) {
		$errors[] = 'You entered a character that was not a number for \'Phone Digits\'. This must be a completely numeric value.';
	}

	if(!empty($_POST['password']) && empty($_POST['password2'])){
		$errors[] = 'You must confirm the new admin password.';
	}
	if(empty($_POST['password']) && !empty($_POST['password2'])){
		$errors[] = 'You confirmed a new admin password, but no new admin password was entered. I\'m confused..';
	}
	if(empty($_POST['protected_area_name'])){
		$errors[] = 'You must enter a name for your protected area. This can simply be &quot;Protected Area&quot;';
	}
	if(!empty($_POST['err_401_doc'])){
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].$_POST['err_401_doc'])){
			$errors[] = 'The 401 error document specified was not found. Please make sure this path is relative to your document root and has a / at the beggining.';
		}
	}

	if(!empty($_POST['password']) && !empty($_POST['password2'])){
		if($_POST['password'] != $_POST['password2']){
			$errors[] = 'The passwords you entered did not match. Try reentering them.';
		} else {
			if(!validate_password($_POST['password'])){
				$errors[] = 'The new admin password is not valid. Make sure it contains at least 6 characters, at max 10 characters, and contains at least one letter and one number. These measures are in place solely for your security.';
			}
		}
	}

	// if everything validated, let's start updating the database
	if(!isset($errors)){
		if($_POST['admin_email'] != $config['admin_email']){
			ConfigUpdateOption('admin_email','Admin Email',$_POST['admin_email'],$mysql['prefix']);
			$updatedoptions[] = 'Admin Email';
		}
		if($_POST['system_email'] != $config['system_messages_email']){
			ConfigUpdateOption('system_messages_email','System Email',$_POST['system_email'],$mysql['prefix']);
			$updatedoptions[] = 'System Email';
		}
		if($_POST['user_welcome_email'] != $config['user_welcome_email']){
			ConfigUpdateOption('user_welcome_email','Welcome Users',$_POST['user_welcome_email'],$mysql['prefix']);
			$updatedoptions[] = 'Welcome Users';
		}
		if($_POST['admin_user_email'] != $config['admin_user_email']){
			ConfigUpdateOption('admin_user_email','Admin Notification',$_POST['admin_user_email'],$mysql['prefix']);
			$updatedoptions[] = 'Admin Notification';
		}
		if($_POST['date_format'] != $config['date_format']){
			ConfigUpdateOption('date_format','Date Format',$_POST['date_format'],$mysql['prefix']);
			$updatedoptions[] = 'Date Format';
		}
		if($_POST['phone_digits'] != $config['phone_digits']){
			ConfigUpdateOption('phone_digits','Phone Digits',$_POST['phone_digits'],$mysql['prefix']);
			$updatedoptions[] = 'Phone Digits';
		}
		if($_POST['deadlock_url'] != $config['deadlock_url']){
			ConfigUpdateOption('deadlock_url','URL to Deadlock',$_POST['deadlock_url'],$mysql['prefix']);
			$updatedoptions[] = 'URL to Deadlock';
		}
		if($_POST['protected_url'] != $config['protected_area_url']){
			ConfigUpdateOption('protected_area_url','Protected Area URL',$_POST['protected_url'],$mysql['prefix']);
			$updatedoptions[] = 'Protected Area URL';
		}

		if($_POST['auth_type'] != $config['digest_auth']){
			ConfigUpdateOption('digest_auth','Authentication Type',$_POST['auth_type'],$mysql['prefix']);
			$regeneratefiles = true;
			$updatedoptions[] = 'Authentication Type';
		}
		if($_POST['protected_area_name'] != $config['protected_area_name']){
			ConfigUpdateOption('protected_area_name','Protected Area Name',$_POST['protected_area_name'],$mysql['prefix']);
			$regeneratefiles = true;
			$updatedoptions[] = 'Protected Area Name';
		}
		if($_POST['debug_mode'] != $config['debug_mode']){
			ConfigUpdateOption('debug_mode','Debug Mode',$_POST['debug_mode'],$mysql['prefix']);
			$updatedoptions[] = 'Debug Mode';
		}
		if($_POST['prune_inactive_users'] != $config['prune_inactive_users']){
			ConfigUpdateOption('prune_inactive_users','Prune Inactive Users',$_POST['prune_inactive_users'],$mysql['prefix']);
			$updatedoptions[] = 'Prune Inactive Users';
		}
		if($_POST['email_user_accept'] != $config['email_user_accept']){
			ConfigUpdateOption('email_user_accept','Status Change Email',$_POST['email_user_accept'],$mysql['prefix']);
			$updatedoptions[] = 'Status Change Email';
		}
		if($_POST['footer'] != $config['bulk_email_footer']){
			ConfigUpdateOption('bulk_email_footer','Default Bulk Email Footer',$_POST['footer'],$mysql['prefix']);
			$updatedoptions[] = 'Default Bulk Email Footer';
		}
		if($_POST['err_401_doc'] != $config['err_401_doc']){
			ConfigUpdateOption('err_401_doc','401 Error Page',$_POST['err_401_doc'],$mysql['prefix']);
			$updatedoptions[] = '401 Error Page';
			$regeneratefiles = true;
		}
		if(!empty($_POST['password'])){
			if($_POST['password'] != $config['admin_pass']){
				ConfigUpdateOption('admin_pass','Admin Password',$_POST['password'],$mysql['prefix']);
				$updatedoptions[] = 'Admin Password';
				$destroy_current_session = 1;
			}
		}


		// update the require phone field. this is a little more complex than the others because if the box is left unchecked
		// no value is submitted.
		if(isset($_POST['require_phone'])){
			if($config['optional_fields_phone'] != 'true'){
				ConfigUpdateOption('optional_fields_phone','Required Fields - Phone','true',$mysql['prefix']);
				$updatedoptions[] = 'Required Fields - Phone';
			}
		} else {
			if($config['optional_fields_phone'] == 'true'){
				ConfigUpdateOption('optional_fields_phone','Required Fields - Phone','false',$mysql['prefix']);
				$updatedoptions[] = 'Required Fields - Phone';
			}
		}

		// update the require country field. more or less the same as the above.
		if(isset($_POST['require_country'])){
			if($config['optional_fields_country'] != 'true'){
				ConfigUpdateOption('optional_fields_country','Required Fields - Country','true',$mysql['prefix']);
				$updatedoptions[] = 'Required Fields - Country';
			}
		} else {
			if($config['optional_fields_country'] == 'true'){
				ConfigUpdateOption('optional_fields_country','Required Fields - Country','false',$mysql['prefix']);
				$updatedoptions[] = 'Required Fields - Country';
			}
		}

		// update the validation methods. this one is even more complex than the checkboxes!
		// here we need to convert the selection menu to 2 options in the database.
		if(isset($_POST['validation'])){
			switch ($_POST['validation']){
				case '1':
				$validate_email = 'true';
				$validate_admin = 'false';
				break;
				case '2':
				$validate_email = 'false';
				$validate_admin = 'true';
				break;
				case '3':
				$validate_admin = 'true';
				$validate_email = 'true';
				break;
				default:
				$validate_admin = 'false';
				$validate_email = 'false';
			}
			if($validate_email != $config['verify_email']){
				if(($config['verify_email'] == 'true') && (count_inactive_users($mysql['prefix']) > 0)){
					if($config['require_admin_approval'] != "true"){
						ConfigUpdateInactiveStatus($mysql['prefix'],'2');
					} else {
						ConfigUpdateInactiveStatus($mysql['prefix'],'1');
					}
					ConfigUpdateInactiveStatus($mysql['prefix']);
					$updatedoptions[] = 'All users that were pending email verification were automatically verified.';
					$regeneratefiles = true;
				}
				ConfigUpdateOption('verify_email','Email Validation',$validate_email,$mysql['prefix']);
				$updatedoptions[] = 'Email Validation';
			}
			if($validate_admin != $config['require_admin_accept']){
				if(($config['require_admin_accept'] == 'true') && (count_pending_users($mysql['prefix']) > 0)){
					ConfigUpdateApprovalStatus($mysql['prefix']);
					$updatedoptions[] = 'All users that were pending approval were automatically approved.';
					$regeneratefiles = true;
				}
				ConfigUpdateOption('require_admin_accept','Admin Approval',$validate_admin,$mysql['prefix']);
				$updatedoptions[] = 'Admin Approval';
			}

		}
		if(isset($regeneratefiles)){
			generate_htpasswd($mysql['prefix']);
			generate_htaccess($mysql['prefix']);
		}
include "admin_header.php";
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="28" colspan="2" class="style2"><br />
    Your configuration was updated successfully. Please wait while you are redirected to the admin panel index page. </td>
  </tr>
  <tr>
    <td height="19" colspan="2"><br /><span class="style2">
    <? if(isset($updatedoptions)): ?>
      The following options were updated:<br />
	  <?php
	  foreach($updatedoptions as $option){
	  	print '- '.$option.'<br />';
	  }
	  ?>
	 <? else: ?>
	 No options were updated.<br />
	 <? endif; ?> 
      <br />
      If you are not redirected within 5 seconds, <a href="./index.php">click here</a>...</span><br />
    <br /><br /></td>
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
    <td height="28" colspan="2" class="style2"><br />
    This page allows you to modify settings which are used throughout Deadlock to customize various features. Once you are finished making changes, click submit to update the settings. If there are any errors, nothing is changed. </td>
  </tr>
  <tr>
    <td colspan="2"><? if (!empty($errors)){ ?>
      <br /><table width="100%" border="0" align="center">
      <tr>
        <td height="20">
		<div class="style9"><strong>Please fix the following errors to continue.</strong>
		<ul>
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
	  <table width="100%" height="100%" border="0" align="center">
  <tr>
    <td width="100%">		 
	<fieldset>
		  <legend>Names</legend>
	        <table width="100%" border="0">
	          <tr>
	            <td width="123"><span class="style5">Protected Area</span><span class="style5">: </span></td>
                <td width="242"><input type="text" name="protected_area_name" value="<?=ConfigTextField(@$_POST['protected_area_name'],$config['protected_area_name'])?>" />
                <a href="#" class="hintanchor" onmouseover="showhint('Enter a name for your protected area. The value entered here will be shown in the login box. This cannot be left blank.', this, event, '150px')">[?]</a></td>
              </tr>
	        </table>
     </fieldset>
	 <br />
	 <fieldset>
		  <legend>Email Addresses</legend>
	        <table width="100%" border="0">
	          <tr>
	            <td width="123"><div align="left"><span class="style5">Admin Email</span><span class="style5">: </span></div></td>
                <td width="242"><div align="left">
                  <input type="text" name="admin_email" value="<?=ConfigTextField(@$_POST['admin_email'],$config['admin_email'])?>" />
                  <a href="#" class="hintanchor" onmouseover="showhint('Enter the administrator\'s email address. This will be used in the from field of outgoing messages.', this, event, '150px')">[?]</a></div></td>
              </tr>
	          <tr>
	            <td class="style5"><div align="left">System Email:</div></td>
                <td><div align="left">
                  <input type="text" name="system_email" value="<?=ConfigTextField(@$_POST['system_email'],$config['system_messages_email'])?>" />
                  <span class="style5"><a href="#" class="hintanchor" onmouseover="showhint('Enter the system\'s email address. This will primarily be used for sending emails to the administrator.', this, event, '150px')">[?]</a></span></div></td>
              </tr>
	          </table>
		    </fieldset><br />
			<fieldset>
			<legend>Email Options</legend>
	        <table width="100%" border="0">   
	          <tr>
	            <td width="151" height="22" class="style5">Welcome Users: </td>
                <td width="282">
                  <label class="style2"><input name="user_welcome_email" type="radio" value="true"<?=ConfigRadioCheck(@$_POST['user_welcome_email'],$config['user_welcome_email'],'on')?> />On</label>
                  <label class="style2"><input type="radio" name="user_welcome_email" value="false"<?=ConfigRadioCheck(@$_POST['user_welcome_email'],$config['user_welcome_email'],'off')?> />Off</label>
                  <a href="#" class="hintanchor" onmouseover="showhint('Send a welcome email to new users. If admin verification is enabled, this email will tell the user that their account is pending verification.', this, event, '150px')">[?]</a></td>
              </tr>
	          <tr>
	            <td height="22" class="style5">New User Notify: </td>
	            <td><label class="style2"><input name="admin_user_email" type="radio" value="true"<?=ConfigRadioCheck(@$_POST['admin_user_email'],$config['admin_user_email'],'on')?> />On</label>
                  <label class="style2"><input type="radio" name="admin_user_email" value="false"<?=ConfigRadioCheck(@$_POST['admin_user_email'],$config['admin_user_email'],'off')?> />Off</label>
                  <a href="#" class="hintanchor" onmouseover="showhint('If this option is enabled, the administrator will receive an email when a user registers.', this, event, '150px')">[?]</a></td>
	            </tr>
	          <tr>
	            <td height="22" class="style5">Status Change Email: </td>
	            <td><label class="style2"><input name="email_user_accept" type="radio" value="true"<?=ConfigRadioCheck(@$_POST['email_user_accept'],$config['email_user_accept'],'on')?> />On</label>
                  <label class="style2"><input type="radio" name="email_user_accept" value="false"<?=ConfigRadioCheck(@$_POST['email_user_accept'],$config['email_user_accept'],'off')?> />Off</label>
				  <a href="#" class="hintanchor" onmouseover="showhint('If this option is enabled, Deadlock will send the user an email when the administrator approves or denies a request. This is only applicable if admin validation is enabled.', this, event, '150px')">[?]</a></td>
	            </tr>
        </table>
		</fieldset>
	        <br />
			<fieldset>
			<legend>Formats and Requirements</legend>
	        <table width="100%" border="0">
	          
	          <tr>
	            <td width="32%" class="style5">Date Format: </td>
                <td width="68%"><select name="date_format" id="date_format">
                  <?=ConfigDateSelects(@$_POST['date_format'],$config['date_format'])?>
                  </select>
                <a href="#" class="hintanchor" onmouseover="showhint('Anywhere a date is shown, for example a user registration date, this is the format the date will be shown in.', this, event, '150px')">[?]</a> </td>
              </tr>
	          <tr>
	            <td class="style5">Phone Digits: </td>
                <td><input name="phone_digits" type="text" size="4" maxlength="2" value="<?=ConfigTextField(@$_POST['phone_digits'],$config['phone_digits'])?>" />
                <a href="#" class="hintanchor" onmouseover="showhint('Set this to how many digits should be required for a phone number. Usually this should be 10.', this, event, '150px')">[?]</a></td>
              </tr>
	          
	          <tr>
	            <td class="style5">Required Fields: </td>
                <td>
                  <label class="style2"><input type="checkbox" name="require_phone" value="true" <?=ConfigCheckboxCheck(@$_POST['submit'],@$_POST['require_phone'],$config['optional_fields_phone'])?>/>Phone</label>
                  <label class="style2"><input type="checkbox" name="require_country" value="true" <?=ConfigCheckboxCheck(@$_POST['submit'],@$_POST['require_country'],$config['optional_fields_country'])?>/>Country</label>
                <a href="#" class="hintanchor" onmouseover="showhint('Select the checkboxes next to the fields that are required in order for a user to register.', this, event, '150px')">[?]</a></td>
              </tr>
	          <tr>
                <td class="style5">Validation:</td>
	            <td><select name="validation">
                    <?=ConfigVerificationSelects(@$_POST['validation'],$config['verify_email'],$config['require_admin_accept'])?>
                  </select>
                  <a href="#" class="hintanchor" onmouseover="showhint('Email validation will require a user to validate their email address before they are able to access the protected area. Admin approval will make it so that you must approve accounts before they are able to access the protected area. If both are enabled, you will be asked to approve accounts after they have validated their email address.', this, event, '200px')">[?]</a></td>
	            </tr>
        </table>
		</fieldset>
	        <br />
			
	        <br />
		<fieldset>
			<legend>Admin Password</legend>
	        <table width="100%" border="0">
	          
	          <tr>
	            <td width="40%" class="style5">New Password:</td>
                <td><input name="password" type="password" id="password" />
                  <a href="#" class="hintanchor" onmouseover="showhint('This is the password used to login to this admin panel. Leave it blank unless you want to change the password. If you decide to change the password, the new password must be 6-10 characters, contain at least one letter and one number, and must be alphanumeric.', this, event, '200px')">[?]</a></td>
	          </tr>
	          <tr>
	            <td class="style5">Confirm Password:</td>
                <td><input name="password2" type="password" id="password2" />
                  <a href="#" class="hintanchor" onmouseover="showhint('Confirm the password you entered above.', this, event, '200px')">[?]</a></td>
              </tr>
        </table>
		</fieldset>
		<br />
			<fieldset>
			<legend>Other Options</legend>
	        <table width="100%" border="0">
	          
	          <tr>
	            <td width="40%" class="style5">Debug Mode: </td>
                <td>
                  <label class="style2"><input name="debug_mode" type="radio" value="true"<?=ConfigRadioCheck(@$_POST['debug_mode'],$config['debug_mode'],'on')?> />On</label>
                  <label class="style2"><input type="radio" name="debug_mode" value="false"<?=ConfigRadioCheck(@$_POST['debug_mode'],$config['debug_mode'],'off')?> />Off</label>
                <a href="#" class="hintanchor" onmouseover="showhint('If there is an internal progrm error, and this option is enabled, the error will be displayed so that the person viewing the page can see it. This is not reccomeded unless you know what you are doing.', this, event, '200px')">[?]</a></td>
              </tr>
	          <tr>
	            <td width="40%" class="style5">Authentication Type:</td>
                <td class="style2">
				  <select name="auth_type">
				  <?=ConfigAuthTypeSelects(@$_POST['auth_type'],$config['digest_auth'])?>
				  </select>
                  <a href="#" class="hintanchor" onmouseover="showhint('Please select the type of authentication you would like to use for your protected area. Digest is by far more secure, but some older browsers do not support it. If you are unable to get digest working, it is possible that either your brower, or your server does not support it.', this, event, '200px')">[?]</a></td>
              </tr>
              <tr>
	            <td width="40%" class="style5">401 Error Page:</td>
                <td class="style2"><input name="err_401_doc" type="text" id="err_401_doc" value="<?=ConfigTextField(@$_POST['err_401_doc'],$config['err_401_doc'])?>" /><a href="#" class="hintanchor" onmouseover="showhint('This is the page that will be displayed when someone enters and invalid login. This path must be relative to your document root. If you want to keep the default error page, leave this field blank.', this, event, '200px')">[?]</a></td>
              </tr>
                <td class="style5">Prune Inactive Users:</td>
	            <td><label class="style2"><input name="prune_inactive_users" type="radio" value="true"<?=ConfigRadioCheck(@$_POST['prune_inactive_users'],$config['prune_inactive_users'],'on')?> />On</label>
                    <label class="style2"><input type="radio" name="prune_inactive_users" value="false"<?=ConfigRadioCheck(@$_POST['prune_inactive_users'],$config['prune_inactive_users'],'off')?> />Off</label>
                  <a href="#" class="hintanchor" onmouseover="showhint('Users that have not validated their email address after 72 hours will automatically be removed.', this, event, '200px')">[?]</a></td>
	            </tr>
	          <tr>
	            <td height="23" colspan="2" valign="bottom" class="style5">Default Bulk Email Footer<a href="#" class="hintanchor" onmouseover="showhint('This is the deafult footer that will appear on the bulk mail page.', this, event, '150px')">[?]</a>: </td>
	            </tr>
	          <tr>
	            <td colspan="2" class="style5"><textarea name="footer" cols="50" rows="6" ><? if(isset($_POST['footer'])) print $_POST['footer']; else print $config['bulk_email_footer']; ?></textarea></td>
	            </tr>
        </table>
		</fieldset></td>
  </tr>
</table>
		<div align="center"><br />
	        	<input type="hidden" name="submit" value="1" />
	            <input type="submit" value="Update Changed Options" />
	            <input type="button" onclick="window.location='<?=$_SERVER['PHP_SELF']?>'" value="Reset" />
	            <br />
        </div>
              </div>
	          <br />
	          <br />
	  </form>    </td>
  </tr>
  
</table>
  <?
include "admin_footer.php";
?>