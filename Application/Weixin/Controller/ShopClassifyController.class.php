<?php
namespace Weixin\Controller;

class ShopClassifyController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$obj = M('ShopClassify');
		$where = array();
		if(I('get.fid')){
			$fid = I('get.fid');
		}else{
			$fid = 0;
		}
		$where['fid'] = $fid;
		$where['token'] = session('token');
		$list = $obj->where($where)->select();
		$this->assign('list',$list);            
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
		$id = I('get.id');
		$obj = M('ShopClassify');
		$where['fid'] = $id;
		$where['token'] = session('token');
		$list = $obj->where($where)->select();
		$ids = array();
		$ids[] = $id;
		if($list){
			//有子分类	
			foreach ($list as $key => $value) {
				$ids[] = $value['id'];
			}
		}
		$where = array();
		$where['id'] = array('in',$ids);
		if($obj->where($where)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}

	}

	public function child_add(){
		if(IS_POST){
			$obj = D('ShopClassify');
			$obj->create();
			if($obj->add()){
				$this->success('添加成功',U('index',array('fid'=>I('get.fid'))));
			}else{
				$this->error('添加失败');
			}
		}else{
			$this->display();
		}
		
	}
}