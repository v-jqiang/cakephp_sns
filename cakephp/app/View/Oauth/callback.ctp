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
	//setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
	$user_message = get_oauthinfo();//根据ID获取用户等基本信息
	echo('授权完成!' . '<br />');
	echo('用户信息：' . $user_message['name'] . '<br />');echo('创建时间：' . $user_message['status']['created_at'] . '<br />');
	echo('<br />');
	echo('<br />');
	echo('API列表:' . '<br />'); 
?>

<a href="/cakephp/statuses/home_timeline">获取当前登录用户及其所关注用户的最新微博</a><br />
<a href="/cakephp/friendships/friends">获取用户的关注列表</a><br />
<a href="/cakephp/accountprofile/privacy">获取用户隐私设置信息</a><br />
<a href="/cakephp/search/suggestions/users">搜索用户时的联想搜索建议</a><br />



<?php
} else {
?>
授权失败。
<?php
}


