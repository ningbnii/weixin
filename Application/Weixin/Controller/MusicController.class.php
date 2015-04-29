<?php
namespace Weixin\Controller;
	
class MusicController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->show_all();
		$this->display();
	}

	public function add(){
		if(IS_POST){
			$this->all_insert();			
		}else{
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
			$this->all_edit();			
		}else{
			$this->show_id();
			$this->display();
		}
	}


	public function del(){
		$this->all_delete();
	}
}