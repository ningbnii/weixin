<?php
namespace Weixin\Controller;
use Think\Controller;

class WeixinController extends Controller{
	/* 空操作，用于输出404页面 */
	//先不写空操作，便于调试，后期功能完善后再开启该方法
/* 	public function _empty(){
		$this->redirect('User/Index');
	} */
	
	public function _initialize(){
		if(!check_login()){
			$this->error('您还没有登录，请先登录', U('Home/User/login'));
		}
		$this->assign('username',$_SESSION['onethink_home']['user_auth']['username']);

	}

	private function check_keyword(){
		$where['token'] = session('token');
		$where['keyword'] = I('post.keyword');
		$info = M('Keyword')->where($where)->find();
		if(!$info){
			return true;
		}else{
			return false;
		}
	}

	protected function show_all(){
		$Obj = D(CONTROLLER_NAME);
		$where['token'] = session('token');
		$list = $Obj->where($where)->select();
		$this->assign('list',$list);  
	}

	protected function show_all_relation(){
		$obj = D(CONTROLLER_NAME);
		$where['token'] = session('token');
		$list = $obj->relation(true)->where($where)->select();
		$this->assign('list',$list);
	}

	protected function show_id(){
		$Obj = M(CONTROLLER_NAME);
		$where['id'] = I('get.id');
		$data = $Obj->where($where)->find();
		$this->assign('data',$data);
	}

	protected function show_token($name){
		if($name){
			$obj = M($name);
		}else{
			$obj = M(CONTROLLER_NAME);
		}	
		$data = $obj->where(array('token'=>session('token')))->find();
		$this->assign('data',$data);
	}

	protected function all_insert(){
		if(!$this->check_keyword()){
			$this->error('关键词已经存在');
		}
		$obj = D(CONTROLLER_NAME);
		$obj->create();
		if($id = $obj->add()){
			$data['pid'] = $id;
			$data['keyword'] = I('post.keyword');
			$data['module'] = CONTROLLER_NAME;
			$keyword = D('Keyword');
			$keyword->create($data);
			if($keyword->add()){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('关键词添加失败');
			}
		}else{
			$this->error('功能添加失败');
		}
	}

	protected function all_delete(){
		$obj = M(CONTROLLER_NAME);
		$where['id'] = I('get.id');
		if($obj->where($where)->delete()){
			$keyword = M('Keyword');
			if($keyword->where(array('pid'=>I('get.id'),'module'=>CONTROLLER_NAME))->find()){
				if($keyword->where(array('pid'=>I('get.id'),'module'=>CONTROLLER_NAME))->delete()){
					$this->success('删除成功');
				}else{
					$this->error('关键词删除失败');
				}			
			}else{
				$this->success('删除成功');
			}
		}else{
			$this->error('功能删除失败');
		}

	}

	protected function all_edit(){
		$obj = D(CONTROLLER_NAME);
		$obj->create();
		$where['id'] = I('post.id');
		if($obj->where($where)->save()){
			$data['pid'] = I('post.id');
			$data['keyword'] = I('post.keyword');
			$data['module'] = CONTROLLER_NAME;
			$keyword = D('Keyword');
			$where = array();
			$where['keyword'] = I('post.keyword');
			$where['token'] = session('token');
			if(I('post.type')){
				$where['type'] = I('post.type');
			}
			if(!$info = $keyword->where($where)->find()){
				
				$keyword->create($data);
				if($keyword->where(array('pid'=>I('post.id'),'token'=>session('token'),'module'=>CONTROLLER_NAME))->save()){
					$this->success('修改成功',U('index'));
				}else{
					$this->error('关键词修改失败');
				}				
			}else{
				$this->success('修改成功',U('index'));
			}
			
		}else{
			$this->error('功能修改失败');
		}
	}

	protected function only_insert(){
			$obj = D(CONTROLLER_NAME);
			$obj->create();
			if($obj->add()){
				$this->success('添加成功',U('index'));
			}else{
				$this->error('添加失败');
			}
	}

	protected function only_edit(){
		$obj = D(CONTROLLER_NAME);
		$obj->create();
		$where = array();
		$where['id'] = I('post.id');
		if($obj->where($where)->save()){
			$this->success('修改成功',U('index'));
		}else{
			$this->error('修改失败');
		}
	}

	protected function only_delete(){
		$obj = M(CONTROLLER_NAME);
		$where = array();
		$where['id'] = I('get.id');
		if($obj->where($where)->delete()){
			$this->success('删除成功',U('index'));
		}else{
			$this->error('删除失败',U('index'));
		}
	}

	protected function api_notice_increment($url, $data){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if ($js['errcode']=='0'){
				return array('rt'=>true,'errorno'=>0);
			}else {
				$this->error('发生错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
			}
		}
	}
	protected function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}

	protected function curlPost($url, $data,$showError=1){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if (intval($js['errcode']==0)){
				return array('rt'=>true,'errorno'=>0,'media_id'=>$js['media_id'],'msg_id'=>$js['msg_id']);
			}else {
				if ($showError){
					$this->error('发生了Post错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
				}
			}
		}
	}

	protected function https_request($url, $data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
}