<?php
namespace Weixin\Controller;
class ShopController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		if(IS_POST){
			if(!I('post.id')){
				$this->all_insert();
			}else{
				$this->all_edit();
			}
		}else{
			$this->show_token();
			$this->display();
		}
	}

}