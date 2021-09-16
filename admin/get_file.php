<?
/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

  error_reporting(E_WARNING);

//========================================================================================================
// Set variables, if they are not registered globally; needs PHP 4.1.0 or higher
//========================================================================================================

  if(isset($_REQUEST['file'])) $file = $_REQUEST['file'];

  if(isset($_SERVER['HTTP_HOST'])) $HTTP_HOST = $_SERVER['HTTP_HOST'];

//========================================================================================================
// Includes
//========================================================================================================

  if($HTTP_HOST == 'localhost' || $HTTP_HOST == '127.0.0.1' || ereg('^192\.168\.0\.[0-9]+$', $HTTP_HOST)) {
    include('config_local.inc.php');
  }
  else {
    include('../config.php');
  }

  if(!isset($language)) $language = 'en';
  include("languages/lang_$language.inc");
  include('fmlib.inc.php');

//========================================================================================================
// Main
//========================================================================================================

  $ftp = false;

  if($ftp_server && $ftp_user) {
    if($ftp = fm_connect()) {
      $file = fm_get($file);
      @ftp_quit($ftp);
    }
  }

  if(file_exists($file)) {
    $filename = basename($file);

    if($replSpacesDownload) $filename = str_replace(' ', '_', $filename);
    if($lowerCaseDownload) $filename = strtolower($filename);

    header("Content-Type: application/octetstream");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($file);

    if($ftp) @unlink($file);
  }
  else {
?>
    <html>
    <head>
    <link rel="stylesheet" href="style.css" type="text/css">
    </head>
    <body>
<?
    fm_view_error($msg['errOpen'] . ": $file");
?>
    </body>
    </html>
<?
  }
?>
