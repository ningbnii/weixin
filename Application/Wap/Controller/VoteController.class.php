<?php
namespace Wap\Controller;

class VoteController extends WapController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$list = M('VoteQuestion')->where(array('token'=>session('token'),'pid'=>I('get.id')))->select();
		foreach ($list as $key => $value) {
			$count = M('VoteRecord')->where(array('token'=>session('token'),'pid'=>$value['id']))->count();
			$list[$key]['count'] = $count;
		}

		$this->assign('list',$list);
		$this->display();
	}

	public function detail(){
		$regflag = M('Vote')->field('regflag')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
		if($regflag['regflag'] == 1){
			//判断是否注册
			$data = M('VoteUser')->where(array('token'=>session('token'),'wecha_id'=>session('wecha_id'),'pid'=>I('get.pid')))->find();
			if(!$data){
				$this->redirect('reg',array('id'=>I('get.pid'),'token'=>session('token')));
			}
		}

		//判断是否已经投过票
		$data = M('VoteRecord')->where(array('token'=>session('token'),'wecha_id'=>session('wecha_id'),'pid'=>I('get.id')))->find();
		if($data){
			$this->redirect('voted',array('id'=>I('get.id'),'pid'=>I('get.pid'),'token'=>session('token')));
		}
		M('VoteQuestion')->where(array('token'=>session('token'),'id'=>I('get.id')))->setInc('click');
		$data = M('VoteQuestion')->where(array('id'=>I('get.id')))->find();
		$data['count'] = M('VoteRecord')->where(array('token'=>session('token'),'pid'=>$data['id']))->count();

		$list = M('VoteItem')->where(array('token'=>session('token'),'pid'=>I('get.id')))->select();

		$this->assign('list',$list);
		$this->assign('data',$data);
		$this->display();
	}

	public function vote(){
		if(IS_POST){
			//是否已经选择过了
			$data = M('VoteRecord')->where(array('pid'=>I('post.pid'),'wecha_id'=>session('wecha_id'),'token'=>session('token')))->find();
			if($data){
				echo 1;exit;
			}
			$data = array();
			$data['select_id'] = I('post.id');
			$data['pid'] = I('post.pid');
			$data['token'] = session('token');
			$data['wecha_id'] = session('wecha_id');
			if(M('VoteRecord')->add($data)){
				echo 1;exit;
			}else{
				echo 2;exit;
			}
		}
	}

	/**
	 * 显示投票结果
	 */
	public function voted(){
		$data = M('VoteQuestion')->where(array('id'=>I('get.id')))->find();
		$data['count'] = M('VoteRecord')->where(array('token'=>session('token'),'pid'=>I('get.id')))->count();
		//一共投了多少票
		$counts = M('VoteRecord')->where(array('token'=>session('token'),'pid'=>I('get.id')))->count();

		$list = M('VoteItem')->where(array('token'=>session('token'),'pid'=>I('get.id')))->select();
		foreach ($list as $key => $value) {
			$count = M('VoteRecord')->where(array('token'=>session('token'),'select_id'=>$value['id']))->count();

			$statistics = round($count/$counts,2)*100;
			$statistics = $statistics.'%';
			$list[$key]['statistics'] = $statistics;
		}

		//判断投的是第几项
		$select = M('VoteRecord')->field('select_id')->where(array('wecha_id'=>session('wecha_id'),'pid'=>I('get.id')))->find();
		$this->assign('select',$select);
		$this->assign('list',$list);
		$this->assign('data',$data);
		$this->display();
	}

	/**
	 * 注册
	 */
	public function reg(){
		if(IS_POST){
			$data = array();
			$data['tel'] = I('post.tel');
			$data['name'] = I('post.name');
			$data['wecha_id'] = session('wecha_id');
			$data['pid'] = I('post.pid');

			if(M('VoteUser')->add($data)){
				echo 1;exit;
			}else{
				echo 2;exit;
			}
		}else{
			$reginfo = M('Vote')->field('reginfo')->where(array('id'=>I('get.id')))->find();
			$this->assign('reginfo',$reginfo);
			$this->display();
		}
	}
}