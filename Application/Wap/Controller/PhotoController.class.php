<?php
namespace Wap\Controller;

class PhotoController extends WapController{
	private $bgpic;
	public function _initialize(){
		parent::_initialize();
		$data = M('Photo')->field('bgpic')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
		$this->bgpic = $data['bgpic'];
	}

	public function index(){
		$list = M('PhotoItems')->where(array('token'=>session('token'),'pid'=>I('get.id')))->select();
		$this->assign('list',$list);
		$this->assign('bgpic',$this->bgpic);
		$this->display();
	}
}