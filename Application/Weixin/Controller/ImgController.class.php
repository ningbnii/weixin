<?php
namespace Weixin\Controller;
class ImgController extends WeixinController{
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
			$classify = $this->getClassify();
			$this->assign('classify',$classify);
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
			$this->all_edit();			
		}else{
			$classify = $this->getClassify();
			$this->show_id();
			$this->assign('classify',$classify);
			$this->display();
		}
	}

	public function del(){
		$this->all_delete();
	}

	/**
	 * 获取图文外链
	 */
	public function getLink(){
		$this->display();
	}

	public function getLinkItem(){
		$where = array();
		$where['token'] = session('token');
		$moduleName = I('get.module');
		$list = M($moduleName)->field('id,title')->where($where)->select();
		$this->assign('list',$list);
		$this->assign('module',$moduleName);
		$this->display();
	}

	protected function getClassify(){
		$classifyObj = M('Classify');
		$where = array();
		$where['token'] = session('token');
		$classify = $classifyObj->field("id,fid,name,path,concat(path,'-',id) as bpath")->where($where)->order('bpath')->select();
		foreach ($classify as $key => $value) {
			$classify[$key]['count'] = count(explode('-', $value['bpath']));
		}
		return $classify;		
	}

}