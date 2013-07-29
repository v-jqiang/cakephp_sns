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
	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
    $privacy_message  = $c->get_privacy(); // done
	  
	echo('privacy_message:' . json_encode($privacy_message) . '<br/>'); 
?>
<a href="/cakephp/Oauth/callback">返回API列表页面</a><br />
<?php
} else {
?>
授权失败。
<?php
}


