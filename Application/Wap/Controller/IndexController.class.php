<?php
namespace Wap\Controller;
class IndexController extends WapController{
	public $flashData; //幻灯片
	public $wxuser; //公众号信息
	public $homeInfo; //首页设置信息
	public $cateMenuFileName; //底部菜单样式
	public $catemenu; //类别

	public function _initialize(){
		parent::_initialize();

		/* 幻灯片 */
		$flashObj = M('Flash');
		$where = array();
		$where['token'] = session('token');
		$this->flashData = $flashObj->where($where)->select();
		

		$wxuserObj = M('Wxuser');
		$this->wxuser = $wxuserObj->where($where)->find();
		
		/*首页设置*/
		$homeObj = M('Home');
		$this->homeInfo = $homeObj->where($where)->find();
		
		$catemenuid = $this->homeInfo['catemenuid'];
		if($catemenuid == false){
			$catemenuid = 0;
		}
		/* 底部导航 */
		$this->cateMenuFileName = 'Public:menuStyle'.$catemenuid;
		
		
		/* 类别 */
		$catemenuObj = M('Catemenu');
		$catemenu = $catemenuObj->where(array('token'=>session('token'),'status'=>1,'fid'=>0))->order('sort desc')->select();
		foreach ($catemenu as $key => $value) {
			$catemenuChildren = $catemenuObj->where(array('token'=>session('token'),'status'=>1,'fid'=>$value['id']))->order('sort desc')->select();
			if($catemenuChildren){
				$catemenu[$key]['vo'] = $catemenuChildren;
				$catemenu[$key]['k'] = count($catemenuChildren); 
			}
		}
		$this->catemenu = $catemenu;
		
		
	}

	public function index(){
		$classifyObj = M('Classify');
		$where = array();
		$where['token'] = session('token');
		$where['status'] = 1;
		$where['fid'] = 0;
		$info = $classifyObj->where($where)->order('sorts')->select();
		$homeObj = M('Home');
		$homeInfo = $homeObj->where($where)->find();
		$tpname = 'index'.$homeInfo['tpid'];
		$this->assign('info',$info);
		$this->assign('tpid',$this->homeInfo['tpid']);
		$this->assign('flash',$this->flashData);
		$this->assign('homeInfo',$this->homeInfo);
		$this->assign('cateMenuFileName',$this->cateMenuFileName);
		$this->assign('catemenu',$this->catemenu);
		$this->assign('wxuser',$this->wxuser);
		$this->display($tpname);
	}

	public function lists(){
		$classifyObj = M('Classify');
		$where = array();
		$where['token'] = session('token');
		$where['fid'] = I('get.classid');
		$where['status'] = 1;
		$info = $classifyObj->where($where)->order('sorts')->select();

		if($info){
			$data = $classifyObj->where(array('id'=>I('get.classid')))->find();
			if($data['channel'] != 0){
				$tpname = 'index'.$data['channel'];
				$tpid = $data['channel'];
			}else{
				$homeInfo = M('Home')->where(array('token'=>session('token')))->find();
				$tpname = 'index'.$homeInfo['tpid'];
				$tpid = $homeInfo['tpid'];
			}
			$this->assign('info',$info);
			$this->assign('flash',$this->flashData);
			$this->assign('tpid',$tpid);
			$this->assign('homeInfo',$this->homeInfo);
			$this->assign('cateMenuFileName',$this->cateMenuFileName);
			$this->assign('catemenu',$this->catemenu);
			$this->assign('wxuser',$this->wxuser);
			$this->display($tpname);
		}else{
			$where = array();
			$where['token'] = session('token');
			$where['status'] = 1;
			$info = $classifyObj->where($where)->order('sorts desc')->select();
			/* 父分类信息 */
			$parentClass = $classifyObj->where(array('id'=>I('get.classid')))->find();
			$listId = $parentClass['list'];

			$imgObj = M('Img');
			$where = array();
			$where['token'] = session('token');
			$where['classid'] = I('get.classid');
			$res = $imgObj->where($where)->order('sorts desc')->select();

			/* 模版选择 */
			$where = array();
			$where['token'] = session('token');
			$where['id'] = I('get.classid');
			$where['status'] = 1; 
			$tpl = $classifyObj->where($where)->find();
			if($tpl['list'] == 0){
				$tplListName = 'list1';
				$listId = 1;
			}else{
				$tplListName = 'list'.$tpl['list'];
			}


			$this->assign('res',$res);
			$this->assign('info',$info);
			$this->assign('listId',$listId);
			$this->assign('homeInfo',$this->homeInfo);
			$this->assign('wxuser',$this->wxuser);
			$this->display($tplListName);
		}
	}

	public function content(){
		$imgObj = M('Img');
		$where = array();
		$where['token'] = session('token');
		$where['id'] = I('get.id');
		$res = $imgObj->where($where)->find();
		

		$classifyObj = M('Classify');
		$where = array();
		$where['token'] = session('token');
		$where['status'] = 1;
		$info = $classifyObj->where($where)->order('sorts desc')->select();

		/*详情页模版选择*/
		$where = array();
		$where['token'] = session('token');
		$where['id'] = I('get.fid');
		$where['status'] = 1;
		$content = M('Classify')->where($where)->find();
		if($content['content'] == 0){
			$content['content'] = 1;			
		}
		$tplContentName = 'content'.$content['content'];	


		/* 往期回顾 */
		$where = array();
		$where['token'] = session('token');
		$where['classid'] = I('get.fid');
		$recent = M('Img')->field('id,text,classid')->where($where)->order('createtime desc')->limit(5)->select();

		$this->assign('recent',$recent);
		$this->assign('content',$content);
		$this->assign('info',$info);
		$this->assign('res',$res);
		
		$this->assign('wxuser',$this->wxuser);
		$this->assign('homeInfo',$this->homeInfo);		
		$this->display($tplContentName);
	}
}