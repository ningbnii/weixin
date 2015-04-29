<?php
namespace Weixin\Controller;

class PhotoController extends WeixinController{
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
		if(M('PhotoItems')->where(array('token'=>session('token'),'pid'=>I('get.id')))->delete()){
			if(M('Photo')->where(array('token'=>session('token'),'id'=>I('get.id')))->delete()){
				if(M('Keyword')->where(array('token'=>session('token'),'pid'=>I('get.id'),'module'=>'Photo'))->delete()){
					$this->success('删除成功');
				}else{
					$this->error('关键词删除失败');
				}
			}else{
				$this->error('相册删除失败');
			}
		}else{
			$this->error('照片删除失败');
		}
	}

	public function items(){
		$photoItemsObj = M('PhotoItems');
		$list = $photoItemsObj->where(array('token'=>session('token'),'pid'=>I('get.pid')))->select();
		$this->assign('list',$list);
		$this->display();
	}

	public function itemsAdd(){
		if(IS_POST){
			$photoItemsObj = D('PhotoItems');
			$photoItemsObj->create();
			if($photoItemsObj->add()){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}else{

			$this->display();
		}
	}

	public function itemsEdit(){
		if(IS_POST){
			$photoItemsObj = D('PhotoItems');
			$photoItemsObj->create();
			if($photoItemsObj->save()){
				$this->success('修改成功',U('items',array('pid'=>I('post.pid'))));
			}else{
				$this->error('修改失败',U('items',array('pid'=>I('post.pid'))));
			}
		}else{
			$data = M('PhotoItems')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
			$this->assign('data',$data);
			$this->display();
		}
	}

	public function itemsDel(){
		$photoItemsObj = M('PhotoItems');
		if($photoItemsObj->where(array('token'=>session('token'),'id'=>I('get.id')))->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
}