<?php
namespace Weixin\Controller;

class QrcodeController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$wxuser = M('Wxuser');
		if(IS_POST){	
			$wxuser->create();
			if($wxuser->where(array('token'=>session('token')))->save()){
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
		}else{
			$data = $wxuser->field('is_qrcode,qrcode_url')->where(array('token'=>session('token')))->find();
			$this->assign('data',$data);
			$this->display();
		}
		
	}
}