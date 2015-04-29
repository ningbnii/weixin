<?php
namespace Wap\Controller;
use Think\Controller;

class QrcodeController extends Controller{
	public function index(){
		$where = array();
		$where['token'] = session('token');
		$wxuserObj = M('Wxuser');
		$data = $wxuserObj->where($where)->find();
		
		$this->display();
	}
}