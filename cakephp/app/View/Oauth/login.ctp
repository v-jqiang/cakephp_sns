<?php
Configure::load('oauth_config');

if($site=='weibo') {
	App::import('Api', 'weibo');
} else if ($site == 'qq') {
	App::import('Api', 'qq');
} else if ($site == 'tencent') {
	App::import('Api', 'tencent');
} else if ($site == 'baidu') {
	App::import('Api', 'baidu');
} else if ($site == 'linkedin') {
	App::import('Api', 'linkedin');
} else {
	App::import('Api', 'weibo');
}


// OAuth 2 Control Flow
if (isset($_GET['error'])) {
	// LinkedIn returned an error
	print $_GET['error'] . ': ' . $_GET['error_description'];
	exit;
} elseif (isset($_GET['code'])) {
	// User authorized your application
	if ($_SESSION['state'] == $_GET['state']) {
		// Get token so you can make API calls
		getAccessToken();
	} else {
		// CSRF attack? Or did you mix up your states?
		exit;
	}
} else { 
	if ((empty($_SESSION['expires_at'])) || (time() > $_SESSION['expires_at'])) {
		// Token has expired, clear the state
		$_SESSION = array();
	}
	if (empty($_SESSION['access_token'])) {
		// Start authorization process
		getAuthorizationCode();
	}
}
