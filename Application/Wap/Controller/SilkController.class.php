<?php
namespace Wap\Controller;

class SilkController extends WapController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->display();
	}
}