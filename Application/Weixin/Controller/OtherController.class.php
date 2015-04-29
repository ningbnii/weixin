<?php
namespace Weixin\Controller;

class OtherController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}
	public function index(){
		$this->show_token();            
		$this->display();
	}

	public function insert(){
		$data = M('Other')->where(array('token'=>session('token')))->find();

		$otherObj = D('Other');
		$otherObj->create();
		if($data){
			//save
			if($otherObj->where(array('id'=>$data['id']))->save()){
				$this->success('修改成功',U('index'));
			}else{
				$this->error('修改失败',U('index'));
			}
		}else{
			//add
			if($otherObj->add()){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('添加失败',U('index'));
			}			
		}
	}}