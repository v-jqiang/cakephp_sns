<?php
session_start();

//申请到的appid
$_SESSION["appid"]    = Configure::read('qq.appid'); 

//申请到的appkey
$_SESSION["appkey"]   = Configure::read('qq.appkey'); 

//QQ登录成功后跳转的地址,请确保地址真实可用，否则会导致登录失败。
$_SESSION["callback"] = "www.thutrip.com";


//QQ授权api接口.按需调用
$_SESSION["scope"] = "get_user_info";


//$appid = $oauth['qq']['appid']; 
//$appkey = $oauth['qq']['appkey']; 
//$scope =  "get_user_info";
//$callback = "www.thutrip.com";

function qq_login($appid, $scope, $callback)
{
    /*$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
	
    $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
        . $appid . "&redirect_uri=" . urlencode($callback)
        . "&state=" . $_SESSION['state']
        . "&scope=".$scope;
    header("Location:$login_url");
	
	
	//-------生成唯一随机串防CSRF攻击
	
	$state = md5(uniqid(rand(), TRUE));
        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );
	
        //$login_url =  combineURL("https://graph.qq.com/oauth2.0/authorize", $keysArr);
		$login_url =	'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=roqaj1a03qat&scope=r_fullprofile+r_emailaddress+rw_nus&state=51f627051c2a64.66089589&redirect_uri=http%3A%2F%2Fwww.thutrip.com%2Fsns%2Flinkedin%2Findex.php';
		echo($login_url);
		header("Location:$login_url");*/
    $state = md5(uniqid(rand(), TRUE));
	$params = array('response_type' => 'code',
					'client_id' => $appid,
					'scope' => $scope,
					'state' => $state, // unique long string
					'redirect_uri' => $callback,
			  );

	// Authentication request
	$url = 'https://graph.qq.com/oauth2.0/authorize?' . http_build_query($params);
	echo($url);
	// Needed to identify request when it returns to us
	$_SESSION['state'] = $params['state'];

	// Redirect user to authenticate
	header("Location: $url");
}



function qq_callback()
{
    //debug
    //print_r($_REQUEST);
    //print_r($_SESSION);

    if($_REQUEST['state'] == $_SESSION['state']) //csrf
    {
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $_SESSION["appid"]. "&redirect_uri=" . urlencode($_SESSION["callback"])
            . "&client_secret=" . $_SESSION["appkey"]. "&code=" . $_REQUEST["code"];

        $response = get_url_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>callback error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }

        $params = array();
        parse_str($response, $params);

        //debug
        //print_r($params);

        //set access token to session
        $_SESSION["access_token"] = $params["access_token"];
    }
    else 
    {
        exit("The state does not match. You may be a victim of CSRF.");
    }
}

function get_openid()
{
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" 
        . $_SESSION['access_token'];

    $str  = get_url_contents($graph_url);
    if (strpos($str, "callback") !== false)
    {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }

    $user = json_decode($str);
    if (isset($user->error))
    {
        echo "<h3>openid error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
    }

    //debug
    //echo("Hello " . $user->openid);

    //set openid to session
    $_SESSION["openid"] = $user->openid;
}

function do_post($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_POST, TRUE); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_URL, $url);
    $ret = curl_exec($ch);

    curl_close($ch);
    return $ret;
}

function get_url_contents($url)
{
    if (ini_get("allow_url_fopen") == "1")
        return file_get_contents($url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result =  curl_exec($ch);
    curl_close($ch);

    return $result;
}

function get_user_info()
{
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
        . "access_token=" . $_SESSION['access_token']
        . "&oauth_consumer_key=" . $_SESSION["appid"]
        . "&openid=" . $_SESSION["openid"]
        . "&format=json";

    $info = get_url_contents($get_user_info);
    $arr = json_decode($info, true);

    return $arr;
}

function connect_to_site()
{
	qq_login($_SESSION["appid"], $_SESSION["scope"], $_SESSION["callback"]);
}

function get_oauthid()
{
	//QQ登录成功后的回调地址,主要保存access token
	qq_callback();

	//获取用户标示id
	get_openid();

	return $_SESSION["openid"];
}

function get_oauthinfo()
{
	$arr = get_user_info();
	$oauth_info = array();
	$oauth_info['openid'] = $_SESSION["openid"];
	$oauth_info['nickname'] = $arr['nickname'];
	$oauth_info['avatar'] = $arr['figureurl_1'];
	$oauth_info['url'] = '';
	return $oauth_info;
}

function del_token() {
	unset($_SESSION["openid"]);
	unset($_SESSION['access_token']);
	unset($_SESSION["appid"]);
	unset($_SESSION['appkey']);
	unset($_SESSION['scope']);
	unset($_SESSION["callback"]);
}

/**
     * combineURL
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        
        return $combined;
    }