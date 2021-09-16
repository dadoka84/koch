<?php
// ----------------------------------------- 
//  The Web Help .com
// ----------------------------------------- 
// remember to replace you@email.com with your own email address lower in this code.

// load the variables form address bar
$subject = $_REQUEST["subject"];
$message = $_REQUEST["message"];
$from = $_REQUEST["from"];

$Name = $_REQUEST["Name"];
$Company = $_REQUEST["Company"];
$Address = $_REQUEST["Address"];
$Telephone = $_REQUEST["Telephone"];
$FAX = $_REQUEST["FAX"];

$verif_box = $_REQUEST["verif_box"];

// remove the backslashes that normally appears when entering " or '
$message = stripslashes($message); 
$subject = stripslashes($subject); 
$from = stripslashes($from); 

// check to see if verificaton code was correct
if(md5($verif_box).'a4xn' == $_COOKIE['tntcon']){
	// if verification code was correct send the message and show this page
	mail("info@koch.ba", 'Online Form: '.$subject, "
	Adresa: $Address
	Firma: $Company
	Ime: $Name
	Telefon: $Telephone
	Fax: $FAX
	Email: $from
	Poruka: $message", "From: $from");
	// delete the cookie so it cannot sent again by refreshing this page
	setcookie('tntcon','');
} else {
	// if verification code was incorrect then return to contact page and show error
	header("Location:".$_SERVER['HTTP_REFERER']."?subject=$subject&from=$from&message=$message&wrong_code=true");
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>E-Mail Sent</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style></head>

<body>
Email uspjesno primljen. <br />
Odgovor ocekujte uskoro.<br />
Hvala.<br />
<br />
Povratak na  <a href="/">pocetnu stranicu</a> ? 
</body>
</html>
