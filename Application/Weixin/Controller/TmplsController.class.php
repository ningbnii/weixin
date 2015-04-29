<?php
namespace Weixin\Controller;

class TmplsController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$obj = M('Home');
		if(IS_POST){
			$data = array();
			$data['tpid'] = I('post.tpid');
			$data['updatetime'] = time();
			if($value = $obj->where(array('token'=>session('token')))->find()){
				//save
				if($obj->where(array('id'=>$value['id']))->save($data)){
					$info['info'] = '修改成功';
				}else{
					$info['info'] = '修改失败2';
					$info['sql'] = $obj->getLastSql();
				}
			}else{
				//insert
				$data['token'] = session('token');
				$data['createtime'] = time();
				if($obj->add($data)){
					$info['info'] = '修改成功';
				}else{
					$info['info'] = '修改失败';
				}
			}
			$this->ajaxReturn($info,'json');
		}else{
			$this->show_token('Home');
			$this->display();
		}

	}

	public function child(){
		if(IS_POST){
			$classifyObj = M('Classify');
			$data = array();
			$data['channel'] = I('post.channel');
			if($classifyObj->where(array('id'=>I('post.id')))->save($data)){
				$info['info'] = '修改成功';
			}else{
				$info['info'] = '修改失败';
			}
			$this->ajaxReturn($info,'json');
		}else{
			$classifyObj = M('Classify');
			$where = array();
			$where['token'] = session('token');
			$where['status'] = 1;
			$where['fid'] = array('neq','0');
			$classifyData = $classifyObj->where($where)->order('sorts desc')->select();
			// 是否有二级分类
			if($classifyData) {
				$arr = array();
				foreach ($classifyData as $key => $value) {
					$arr[] = $value['fid'];
				}

				$where = array();
				$where['id'] = array('in',$arr);
				$classifyData = $classifyObj->where($where)->order('sorts desc')->select();
				$this->assign('classifyData',$classifyData);
			}

			$homeObj = M('Home');
			$homeData = $homeObj->where(array('token'=>session('token')))->find();
			$this->assign('homeData',$homeData);
			$this->display();	
		}
		
	}

	public function channel(){
		$this->display();
	}

	public function listTp(){
		if(IS_POST){

			$classifyObj = M('Classify');
			$data = array();
			$data['list'] = I('post.list');
			if($classifyObj->where(array('id'=>I('post.id')))->save($data)){
				$info['info'] = '修改成功';
			}else{
				$info['info'] = '修改失败';
			}
			$this->ajaxReturn($info,'json');
		}else{
			$classifyObj = M('Classify');
			$where = array();
			$where['token'] = session('token');
			$where['fid'] = array('neq','0');
			$where['status'] = 1;
			$children = $classifyObj->where($where)->select();
			$where = array();
			$where['token'] = session('token');
			if($children){
				$noInArr = array();
				foreach ($children as $key => $value) {
					$noInArr[] = $value['fid'];
				}
				$where['id'] = array('not in',$noInArr);				
			}

			$where['fid'] = array('eq','0');
			$where['status'] = 1;
			$parent = $classifyObj->where($where)->select();
			if($children) {
				$classifyData = array_merge($children,$parent);				
			}else{
				$classifyData = $parent;
			}
			$this->assign('classifyData',$classifyData);				
			$this->display();
		}
	}

	public function listSelect(){
		$this->display();
	}

	/*详情页模版选择*/
	public function contentTp(){
		if(IS_POST){
			$classifyObj = M('Classify');
			$data = array();
			$data['content'] = I('post.content');
			if($classifyObj->where(array('id'=>I('post.id')))->save($data)){
				$info['info'] = '修改成功';
			}else{
				$info['info'] = '修改失败';
			}
			$this->ajaxReturn($info,'json');
		}else{
			$classifyObj = M('Classify');
			$where = array();
			$where['token'] = session('token');
			$where['fid'] = array('neq','0');
			$where['status'] = 1;
			$children = $classifyObj->where($where)->select();

			$where = array();
			$where['token'] = session('token');
			if($children){
				$noInArr = array();
				foreach ($children as $key => $value) {
					$noInArr[] = $value['fid'];
				}
				$where['id'] = array('not in',$noInArr);				
			}

			$where['fid'] = array('eq','0');
			$where['status'] = 1;
			$parent = $classifyObj->where($where)->select();

			if($children){
				$classifyData = array_merge($children,$parent);
			}else{
				$classifyData = $parent;
			}

			$this->assign('classifyData',$classifyData);
			$this->display();
		}
	}

}
