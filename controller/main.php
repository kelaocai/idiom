<?php
class main extends spController {
	function index() {
		
		import("tencent.php");
		//require_once APP_PATH . '/include/config.php';
		import('config.php',true,true);
		OAuth::init(APP_ID, APP_SECRET);
		Tencent::$debug = $debug;

		//打开session
		session_start();
		header('Content-Type: text/html; charset=utf-8');

		if ($_SESSION['t_access_token'] || ($_SESSION['t_openid'] && $_SESSION['t_openkey'])) {//用户已授权
			//echo '<pre><h3>已授权</h3>用户信息：<br>';
			//获取用户信息
			$r = Tencent::api('user/info');
			$callback = 'http://' . $_SERVER['HTTP_HOST'] .'/idiom/guess';
			header('Location: ' . $callback);
			//print_r(json_decode($r, true));
			//echo '</pre>';
			
		} else {//未授权
			$callback = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		
			//回调url
			if ($_GET['code']) {//已获得code
				$code = $_GET['code'];
				$openid = $_GET['openid'];
				$openkey = $_GET['openkey'];
				//获取授权token
				$url = OAuth::getAccessToken($code, $callback);
				$r = Http::request($url);
				parse_str($r, $out);
				//存储授权数据
				if ($out['access_token']) {
					$_SESSION['t_access_token'] = $out['access_token'];
					$_SESSION['t_refresh_token'] = $out['refresh_token'];
					$_SESSION['t_expire_in'] = $out['expires_in'];
					$_SESSION['t_code'] = $code;
					$_SESSION['t_openid'] = $openid;
					$_SESSION['t_openkey'] = $openkey;

					//验证授权
					$r = OAuth::checkOAuthValid();
					if ($r) {
						header('Location: ' . $callback);
						//刷新页面
					} else {
						exit('<h3>授权失败,请重试</h3>');
					}
				} else {
					exit($r);
				}
			} else {//获取授权code
				if ($_GET['openid'] && $_GET['openkey']) {//应用频道
					$_SESSION['t_openid'] = $_GET['openid'];
					$_SESSION['t_openkey'] = $_GET['openkey'];
					//验证授权
					$r = OAuth::checkOAuthValid();
					if ($r) {
						header('Location: ' . $callback);
						//刷新页面
					} else {
						exit('<h3>授权失败,请重试</h3>');
					}
				} else {
					$url = OAuth::getAuthorizeURL($callback);
					header('Location: ' . $url);
				}
			}
		}
	}

	function gonext() {

	}

	function judge() {
		//echo "answer:".$this->spArgs('answer');

		$result = array( array('score' => 100));
		echo json_encode($result);

	}

}
