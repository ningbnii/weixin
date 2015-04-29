<?php
namespace Wap\Controller;

class ReplyController extends WapController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$list = M('ReplyData')->field('content,createtime,type')->where(array('token'=>session('token'),'wecha_id'=>session('wecha_id')))->order('id asc')->select();
		$this->assign('list',$list);
		$this->display();
	}

	public function reply(){
		if(IS_POST){
			$replyDataObj = D('ReplyData');
			$replyDataObj->create();
			if($id = $replyDataObj->add()){
				$data = M('ReplyData')->field('content,createtime')->where(array('id'=>$id))->find();
				$data['createtime'] = date('Y-m-d H:i:s',$data['createtime']);
				$json['info'] = '添加成功';
				$json['data'] = $data;
				$this->ajaxReturn($json,'json');
			}else{
				$this->error('添加失败');
			}
		}
	}

	/**
	 * 获取回复的最新内容
	 */
	public function getreply(){
		$where = array();
		$where['token'] = session('token');
		$where['wecha_id'] = session('wecha_id');
		$where['type'] = '1';

		$where['createtime'] = array('gt',time()-5);
		
		$data = M('ReplyData')->field('id,content,createtime')->where($where)->find();

		if($data){
			if(empty($_SESSION['replyid'])){
				session('replyid',$data['id']);
			}
			if(session('replyid') != $data['id']){
				session('replyid',$data['id']);
				$data['createtime'] = date('Y-m-d H:i:s',$data['createtime']);
				$json = array();
				$json['info'] = '1';
				$json['data'] = $data;
				$this->ajaxReturn($json,'json');
			}

		}
	}

}