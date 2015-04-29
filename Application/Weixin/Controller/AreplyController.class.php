<?php
namespace Weixin\Controller;

class AreplyController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->show_token();
		
		$this->display();
	}

	public function insert(){
		$data = M('Areply')->where(array('token'=>session('token')))->find();

		$areplyObj = D('Areply');
		$areplyObj->create();
		if($data){
			//save
			if($areplyObj->where(array('id'=>$data['id']))->save()){
				$this->success('修改成功',U('index'));
			}else{
				$this->error('修改失败',U('index'));
			}
		}else{
			//add
			if($areplyObj->add()){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('添加失败',U('index'));
			}			
		}
	}
}