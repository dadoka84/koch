<?
require('../config.php');
require('../global.php');
// connect to the database
db_connect($mysql['username'],$mysql['password'],$mysql['database'],$mysql['host']);

// get variables
$filepath = $_GET['filepath'];
$filename = $_GET['filename'];
$username = $_GET['username'];
$fullfile = $filepath . $filename;

// log downloads
Logdownload($mysql['prefix'],$username,$filename);

// send file to user browser	
$fp = fopen($fullfile, 'r');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-type: application/octet-stream');
header("Content-Disposition: attachment; filename=".$filename."");
header("Content-Length: ".filesize($fullfile));

fpassthru($fp);


?>