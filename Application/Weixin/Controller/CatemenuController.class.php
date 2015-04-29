<?php
namespace Weixin\Controller;

class CatemenuController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$list = $this->getCatemenu();
		$this->assign('list',$list);
		$this->display();
	}

	public function add(){
		if(IS_POST){
			$this->only_insert();
		}else{
			$catemenu = $this->getCatemenu(0);
			$this->assign('catemenu',$catemenu);
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
			$this->only_edit();
		}else{
			$catemenu = $this->getCatemenu(0);
			$this->assign('catemenu',$catemenu);
			$this->show_id();
			$this->display();
		}
	}

	public function del(){
		$id = I('get.id');
		$obj = M('Catemenu');
		$where['fid'] = $id;
		$where['token'] = session('token');
		$list = $obj->where($where)->select();
		$ids = array();
		$ids[] = $id;
		if($list){
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

	public function styleSet(){
		$obj = M('Home');
		if(IS_POST){
			$data = array();
			$data['catemenuid'] = I('post.catemenuid');
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

	public function plugmenu(){
		if(IS_POST){
			$homeObj = D('Home');
			$homeObj->create();
			$homeData = array();
			$homeData['plugmenu_color'] = I('post.color');
			if($homeObj->where(array('token'=>session('token')))->find()){
				if($homeObj->where(array('token'=>session('token')))->save($homeData)){
					$this->success('操作成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				if($homeObj->add($homeData)){
					$this->success('操作成功');
				}else{
					$this->error('操作失败');
				}					
			}


		}else{
			$homeObj = M('Home');
			$homeData = $homeObj->where(array('token'=>session('token')))->find();
			$this->assign('homeData',$homeData);

			$this->display();
		}
	}

	protected function getCatemenu($fid){
		$catemenuObj = M('Catemenu');
		$where = array();
		$where['token'] = session('token');
		$where['status'] = 1;
		if($fid===0){
			$where['fid'] = $fid;	
		}
		
		$catemenu = $catemenuObj->field("id,fid,name,url,status,sort,createtime,updatetime,path,concat(path,'-',id) as bpath")->where($where)->order('bpath')->select();
		foreach ($catemenu as $key => $value) {
			$catemenu[$key]['count'] = count(explode('-', $value['bpath']));
		}
		return $catemenu;
	}
}