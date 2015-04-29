<?php
namespace Weixin\Controller;
class FlashController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->show_all();            
		$this->display();
	}

	public function add(){
		if(IS_POST){
			$this->only_insert();
		}else{
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
			$this->only_edit();			
		}else{
			$this->show_id();
			$this->display();
		}
	}

	public function del(){
		$this->only_delete();
	}

}