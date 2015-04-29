<?php
namespace Weixin\Controller;

class ShopProductsController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$this->show_all_relation();
		$this->display();
	}

	public function add(){
		if(IS_POST){
			$this->only_insert();
		}else{
			$classify = $this->getClassify();
			$this->assign('classify',$classify);
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
			$this->only_edit();
		}else{
			$classify = $this->getClassify();
			$this->show_id();
			$this->assign('classify',$classify);
			$this->display();
		}
	}

	public function del(){
		$this->only_delete();
	}

	protected function getClassify(){
		$classifyObj = M('ShopClassify');
		$where = array();
		$where['token'] = session('token');
		$where['status'] = 1;
		$classify = $classifyObj->field("id,fid,name,path,concat(path,'-',id) as bpath")->where($where)->order('bpath')->select();
		foreach ($classify as $key => $value) {
			$classify[$key]['count'] = count(explode('-',$value['bpath']));
		}
		return $classify;
	}
}