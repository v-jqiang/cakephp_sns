<?php
Configure::load('oauth_config');
App::import('Api', 'weibo');

if (isset($_REQUEST['code']) && empty($_SESSION['token'])) {
	try {
		$token = getAccessToken() ;
	} catch (OAuthException $e) {
	}
} else {
   $token =  $_SESSION['token'];
}


if ($token) {
	$_SESSION['token'] = $token;
	$friends_message = get_oauthinfo();//根据ID获取用户等基本信息
	echo('friends:' . '<br />'); 
	echo('关注人数：' . $friends_message['followers_count'] . '<br />');echo('朋友人数：' . $friends_message['friends_count'] . '<br />');
?>
<a href="/cakephp/Oauth/callback">返回API列表页面</a><br />
<?php
} else {
?>
授权失败。
<?php
}


