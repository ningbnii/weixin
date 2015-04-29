<?php
namespace Wap\Controller;

class ShopController extends WapController{
	public $shopFlashData; //商城幻灯片
	
	public function _initialize(){
		parent::_initialize();

		//幻灯片
		$shopFlashObj = M('ShopFlash');
		$where = array();
		$where['token'] = session('token');
		$this->shopFlashData = $shopFlashObj->where($where)->select();

	}

	public function index(){
		$this->assign('flash',$this->shopFlashData);
		$this->display();
	}
}