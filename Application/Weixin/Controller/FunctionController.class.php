<?php
namespace Weixin\Controller;
class FunctionController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
		session('token',I('get.token'));
	}

	public function index(){
		$this->display();
	}
}