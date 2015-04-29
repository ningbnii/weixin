<?php
namespace Weixin\Controller;
class HomeController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		if(IS_POST){
			if(I('post.id')){
				
				$this->all_edit();
			}else{
				$this->all_insert();
			}
			
		}else{
			$this->show_token();
			$this->display();			
		}

	}

}