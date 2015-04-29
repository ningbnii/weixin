<?php
namespace Weixin\Controller;

class VoteController extends WeixinController{
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
		//vote
		if(!M('Vote')->where(array('token'=>session('token'),'id'=>I('get.id')))->delete()){
			$this->error('项目删除失败');
		}
		if(!M('Keyword')->where(array('token'=>session('token'),'pid'=>I('get.id'),'module'=>'vote'))->delete()){
			$this->error('关键词删除失败');
		}
		if(!M('VoteQuestion')->where(array('token'=>session('token'),'pid'=>I('get.id')))->delete()){
			$this->error('投票问题删除失败');
		}
		if(!M('VoteItem')->where(array('token'=>session('token'),'itemid'=>I('get.id')))->delete()){
			$this->error('选项删除失败');
		}
		$this->success('删除成功');
	}

	public function itemlist(){
		$list = M('VoteQuestion')->where(array('token'=>session('token'),'pid'=>I('get.id')))->select();
		$this->assign('list',$list);
		$this->display();
	}
/**
 * 新增投票问题
 */
	public function itemadd(){
		if(IS_POST){
			$voteQuestionObj = D('VoteQuestion');
			$voteQuestionObj->create();
			if($voteQuestionObj->add()){
				$this->success('添加成功',U('itemlist',array('id'=>I('post.pid'))));
			}else{
				$this->error('添加失败',U('itemlist',array('id'=>I('post.pid'))));
			}
		}else{
			$this->display();
		}
	}	

/**
 * 编辑投票问题
 */
	public function itemedit(){
		if(IS_POST){
			$voteQuestionObj = D('VoteQuestion');
			$voteQuestionObj->create();
			if($voteQuestionObj->save()){
				$this->success('修改成功',U('itemlist',array('id'=>I('post.pid'))));
			}else{
				$this->error('修改失败',U('itemlist',array('id'=>I('post.pid'))));
			}
		}else{
			$data = M('VoteQuestion')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
			$this->assign('data',$data);
			$this->display();
		}
	}

/**
 * 删除投票问题
 */
	public function itemdel(){
		$voteQuestionObj = M('VoteQuestion');
		if($voteQuestionObj->where(array('token'=>session('token'),'id'=>I('get.id')))->delete()){
			if(M('VoteItem')->where(array('token'=>session('token'),'pid'=>I('get.id')))->delete()){
				$this->success('删除成功');
			}else{
				$this->error('选项删除失败');
			}
		}else{
			$this->error('问题删除失败');
		}
	}

	/**
	 * 选项列表
	 */
	public function selectlist(){
		$list = M('VoteItem')->where(array('token'=>session('token'),'pid'=>I('get.id')))->select();
		$this->assign('list',$list);
		$this->display();
	}
	/**
	 * 添加选项
	 */
	public function selectadd(){
		if(IS_POST){
			$voteItemObj = D('VoteItem');
			$voteItemObj->create();
			if($voteItemObj->add()){
				$this->success('添加成功');
			}else{
				$this->error('添加失败',U('selectlist',array('id'=>I('post.pid'))));
			}
		}else{
			$this->display();
		}
	}

	/**
	 * 修改选项
	 */
	public function selectedit(){
		if(IS_POST){
			$voteItemObj = D('VoteItem');
			$voteItemObj->create();
			if($voteItemObj->save()){
				$this->success('修改成功',U('selectlist',array('id'=>I('post.pid'),'pid'=>I('post.itemid'))));
			}else{
				$this->error('修改失败',U('selectlist',array('id'=>I('post.pid'),'pid'=>I('post.itemid'))));
			}
		}else{
			$data = M('VoteItem')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
			$this->assign('data',$data);
			$this->display();
		}
	}

	/**
	 * 删除选项
	 */
	public function selectdel(){
		if(M('VoteItem')->where(array('token'=>session('token'),'id'=>I('get.id')))->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

}