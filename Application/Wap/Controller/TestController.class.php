<?php
namespace Wap\Controller;

class TestController extends WapController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->display();
	}
}