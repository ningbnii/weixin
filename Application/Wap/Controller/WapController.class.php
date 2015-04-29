<?php 
namespace Wap\Controller;
use Think\Controller;

class WapController extends Controller{
	public $appId;
	public $appSecret;
	public $signPackage;
	public $wxuserData;
	public function _initialize(){
		session('token',I('get.token'));
		session('wecha_id',I('get.wecha_id'));
		$wxuserObj = M('Wxuser');
		$this->wxuserData = $wxuserObj->where(array('token'=>session('token')))->find();
		$this->appId = $this->wxuserData['appid'];
		$this->appSecret = $this->wxuserData['appsecret'];
		$this->signPackage = $this->getSignPackage();
		session('appId', $this->signPackage['appId']);
		session('timestamp', $this->signPackage['timestamp']);
		session('nonceStr', $this->signPackage['nonceStr']);
		session('signature', $this->signPackage['signature']);

		if(!check_login()){
			if(!$this->is_weixin()){
				if($this->wxuserData['is_qrcode'] == 1 && $this->wxuserData['qrcode_url'] != ''){
					redirect($this->wxuserData['qrcode_url']);
				}else{
					$this->redirect('Wap/Qrcode/index');
				}
				
			}			
		}

	}

	private function is_weixin(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
			return true;
		}
		return false;
	}

	private function getSignPackage(){
		$jsapiTicket = $this->getJsApiTicket();

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		//这里参数的顺序要按照key值ASCII码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			'appId' => $this->appId,
			'nonceStr' => $nonceStr,
			'timestamp' => $timestamp,
			'url' => $url,
			'signature' => $signature,
			'rawString' => $string
			);
		return $signPackage;
	}

	private function createNonceStr($length = 16){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = '';
		for ($i=0; $i < $length; $i++) { 
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket(){
		//jsapi_ticket 应该全局存储与更新
		$accessToken = $this->getAccessToken();
		if($this->wxuserData['ticket'] != '' && ((time() - $this->wxuserData['ticket_createtime']) < 7200)){
			$ticket = $this->wxuserData['ticket'];
		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			$data = array();
			$data['ticket'] = $ticket;
			$data['ticket_createtime'] = time();
			M('Wxuser')->where(array('token'=>session('token')))->save($data);	
		}

		return $ticket;

	}

	private function getAccessToken(){
		//access_token应该全局更新与存储
		if($this->wxuserData['access_token'] != '' && ((time() - $this->wxuserData['access_token_createtime']) < 7200)){
			$access_token = $this->wxuserData['access_token'];
		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			$data = array();
			$data['access_token'] = $access_token;
			$data['access_token_createtime'] = time();
			M('Wxuser')->where(array('token'=>session('token')))->save($data);			
		}

		return $access_token;
	}

	private function httpGet($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}
}

?>