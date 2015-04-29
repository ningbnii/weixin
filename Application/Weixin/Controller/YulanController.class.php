<?php
namespace Weixin\Controller;
class YulanController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->display();
	}
}