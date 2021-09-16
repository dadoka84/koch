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

// check if the user exists, just to prevent errors. if they exist, remove them from the database
if(check_user_exists($_GET['user'],$mysql['prefix'])){
	remove_user($_GET['user'],$mysql['prefix']);
	generate_htpasswd($mysql['prefix']);
}

if(isset($_GET['r']) && $_GET['r']=='inactive'){
	redirect('./inactiveusers.php');
}

// redirect back to the user list regardless of whether or not the operation was successful
redirect('./userlist.php');

?>