<?php
namespace Weixin\Controller;
use Think\Controller;
use ORG\Net\Http;
use OT\PclZip;


class TestController extends Controller{
	public function index(){
		$http = new Http();
		$remote = 'http://wxbuluo.com/update.php';
		$http->curlDownload('http://wxbuluo.com/Update.php','Update.zip');
/* 		if(!is_dir('Update')){
			mkdir('Update');
		} */
// 		rename('Update.zip', './Update/Update.zip');
		$archive = new PclZip('Update.zip');
		if($archive->extract(PCLZIP_OPT_PATH,'./') == 0){
			die("error:".$archive->errorInfo());
		}else {
			unlink('Update.zip');
		}
		
			
		//MoveFolderFiles('./Update/Update', './');

// 		@unlink('./Update/Update.zip');


		// dump($http->fsockopenDownload('http://yuncode.net/code/c_5340fdf2e4ba378'));
		

	}
	
	public function test(){
		// $str = file_get_contents('http://update.wxbuluo.com/home/license/index/domain/'.$_SERVER['SERVER_NAME']);
		echo 'http://update.wxbuluo.com/home/license/index/domain/'.$_SERVER['SERVER_NAME'];
	}
}
