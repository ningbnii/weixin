<?php
namespace Weixin\Controller;

class ReplyController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$list = M('ReplyData')->field('id,wecha_id,content,createtime')->where(array('token'=>session('token'),'type'=>'0'))->group('wecha_id')->order('id desc')->select();
		foreach ($list as $key => $value) {
			$data = M('ReplyData')->field('content,createtime')->where(array('wecha_id'=>$value['wecha_id'],'type'=>'1'))->order('id desc')->find();
			$list[$key]['reply_content'] = $data['content'];
			$list[$key]['reply_createtime'] = $data['createtime'];
		}
		
		$this->assign('list',$list);
		
		$this->display();
	}

	/**
	 * 留言板配置
	 */
	public function config(){
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

	/**
	 * 回复
	 */
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
		}else{
			$list = M('ReplyData')->field('content,createtime,type')->where(array('token'=>session('token'),'wecha_id'=>I('get.wecha_id')))->order('id asc')->select();
			$this->assign('list',$list);
			$this->display();			
		}

	}

	/**
	 * 获取留言
	 */
	public function getreplydata(){
		$where = array();
		$where['token'] = session('token');
		$where['wecha_id'] = I('post.wecha_id');
		$where['type'] = '0';
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

				$this->ajaxReturn($json);				
			}

		}
	}

}