<?php
namespace Weixin\Controller;

class DiymenController extends WeixinController{
	public function _initialize(){
		parent::_initialize();
	}

	public function index(){
		$list = $this->getDiymen();
		$this->assign('list',$list);
		$this->display();
	}

	public function add(){
		if(IS_POST){
			$this->only_insert();
		}else{
			$diymen = $this->getDiymen(0,1);
			$this->assign('diymen',$diymen);
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
			$this->only_edit();
		}else{
			$diymen = $this->getDiymen(0,1);
			$this->assign('diymen',$diymen);
			$this->show_id();
			$this->display();
		}
	}

	public function del(){
		$id = I('get.id');
		$obj = M('Diymen');
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

	public function create(){
		//检查是否填写了appid和appsecret
		$wxuserObj = M('Wxuser');
		$where = array();
		$where['token'] = session('token');
		$wxuserData = $wxuserObj->where($where)->find();
		
		if($wxuserData['appid'] == '' || $wxuserData['appsecret'] == ''){
			$this->error('请填写appid和appsecret',U('Index/edit',array('id'=>$wxuserData['id'])));
		}


		$url_get = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wxuserData['appid'].'&secret='.$wxuserData['appsecret'];
		$json = json_decode($this->curlGet($url_get));
		if($json->errmsg){
			$info['info'] = '获取access_token发生错误：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg.'<br><br>请检查appid和appsecret填写是否正确';
			$this->ajaxReturn($info,'json');
		}

		$data = '{"button":[';

		$class=M('Diymen')->where(array('token'=>session('token'),'fid'=>0,'is_show'=>1))->limit(3)->order('sort desc')->select();

		$kcount=count($class);
		
		$k=1;
		foreach($class as $key=>$vo){
			//主菜单

			$data.='{"name":"'.$vo['title'].'",';
			$c=M('Diymen')->where(array('token'=>session('token'),'fid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->select();
			$count=count($c);
			//子菜单
			$vo['url']=str_replace(array('&amp;'),array('&'),$vo['url']);
			if($c!=false){
				$data.='"sub_button":[';
			}else{
				if(!$vo['url']){
					$data.='"type":"click","key":"'.$vo['keyword'].'"';
				}else {
					$data.='"type":"view","url":"'.$vo['url'].'"';
				}
			}
			$i=1;
			foreach($c as $voo){
				$voo['url']=str_replace(array('&amp;'),array('&'),$voo['url']);
				if($i==$count){
					if($voo['url']){
						$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"}';
					}else{
						$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"}';
					}
				}else{
					if($voo['url']){
						$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"},';
					}else{
						$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"},';
					}
				}
				$i++;
			}
			if($c!=false){
				$data.=']';
			}

			if($k==$kcount){
				$data.='}';
			}else{
				$data.='},';
			}
			$k++;
		}
		$data.=']}';

		//file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$json->access_token);

		$url = 'http://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$json->access_token;
		$rt = $this->api_notice_increment($url,$data);
		if($rt['rt'] == false){
			$info['info'] = '操作失败,curl_error:'.$rt['errorno'];
			$this->ajaxReturn($info,'json');
		}else{
			$info['info'] = '菜单生成成功，请在微信中查看';
			$this->ajaxReturn($info,'json');
		}

	}

	protected function getDiymen($fid,$is_show){
		$diymenObj = M('Diymen');
		$where = array();
		$where['token'] = session('token');
		if($is_show == 1){
			$where['is_show'] = $is_show;	
		}

		if($fid===0){
			$where['fid'] = $fid;	
		}
		
		$diymen = $diymenObj->field("id,fid,title,keyword,url,is_show,sort,createtime,updatetime,path,concat(path,'-',id) as bpath")->where($where)->order('bpath')->select();
		foreach ($diymen as $key => $value) {
			$diymen[$key]['count'] = count(explode('-', $value['bpath']));
		}
		return $diymen;
	}
}