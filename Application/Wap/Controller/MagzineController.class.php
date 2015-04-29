<?php
namespace Wap\Controller;

class MagzineController extends WapController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$where = array();
		$where['token'] = session('token');
		$where['id'] = I('get.id');
		$magzineObj = M('Magzine');
		$data = $magzineObj->where($where)->find();
		$this->assign('data',$data);
		$this->display();
	}
}