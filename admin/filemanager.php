<?
/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

  header('Cache-control: private, no-cache, must-revalidate');
  header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
  header('Date: Sat, 01 Jan 2000 00:00:00 GMT');
  header('Pragma: no-cache');
?>
<html>
<head>
<title>File Manager</title>
</head>
<body style="background-color:#F0F0F0">
<table border="0" width="100%" height="90%"><tr>
<td>
<?
  include('filemanager.inc.php');
?>
</td>
</tr></table>
</body>
</html>
