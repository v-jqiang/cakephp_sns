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
?>
<h2 align="left">查询用户</h2>
	<form action="" >
		<input type="text" name="name" style="width:120px" />
		<input type="submit" />
	</form>
<a href="/cakephp3/Oauth/callback">返回API列表页面</a><br />
<?php
if( isset($_REQUEST['name']) ) {
	$ret = $c->search_users( $_REQUEST['name'] );	//发送微博
	if ( isset($ret['error_code']) && $ret['error_code'] > 0 ) {
		echo "<p>发送失败，错误：{$ret['error_code']}:{$ret['error']}</p>";
	} else {
		echo "<p>发送成功</p>";
	}
	?>
	<?php if( is_array( $ret) ): ?>
	<?php foreach(  $ret as $key=>$item ): ?>
	<div style="padding:10px;margin:5px;border:1px solid #ccc">
		<?=$item['screen_name'];?>
	</div>
	<?php endforeach; ?>
	<?php endif; ?>
	<?
}
?>
<?php
} else {
?>
授权失败。
<?php
}
