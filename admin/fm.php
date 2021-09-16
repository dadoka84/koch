<?php

// include needed files
require('../config.php');
require('../global.php');

// connect to the database
db_connect($mysql['username'], $mysql['password'], $mysql['database'],$mysql['host']);

// assign config options from database to an array
$config = get_config($mysql['prefix']);

debug_mode($config['debug_mode']);

// remove users that have not verified their email after 72 hours if email verification is enabled
if($config['verify_email']=='true' && $config['prune_inactive_users']=='true'){
	PruneInactiveUsers($mysql['prefix']);
}

// start the session
admin_sessions($config['admin_session_expire']);
if (!isset($_SESSION['logged_in']))
{
	redirect('./login.php');
}

// start header
  header('Cache-control: private, no-cache, must-revalidate');
  header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
  header('Date: Sat, 01 Jan 2000 00:00:00 GMT');
  header('Pragma: no-cache');
  
// file manager
include "admin_header_fm.php";
  include('filemanager.inc.php');
include "admin_footer.php";
?>
