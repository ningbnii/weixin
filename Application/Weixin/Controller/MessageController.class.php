<?php
namespace Weixin\Controller;

class MessageController extends WeixinController{
	private $access_token;
	
	public function _initialize(){
		parent::_initialize();
		//检查是否填写了appid和appsecret

		
		
	}

	/**
	 * access_token
	 * access_token的生命周期是两个小时，超过两个小时要重新获取access_token;
	 */
	private function get_access_token(){
		$wxuserObj = M('Wxuser');
		$where = array();
		$where['token'] = session('token');
		$wxuserData = $wxuserObj->where($where)->find();
		if($wxuserData['access_token'] != '' && ((time()-$wxuserData['access_token_createtime']) < 7200)){
			$this->access_token = $wxuserData['access_token'];
		}else{

			if($wxuserData['appid'] == '' || $wxuserData['appsecret'] == ''){
				$this->error('请填写appid和appsecret',U('Index/edit',array('id'=>$wxuserData['id'])));
			}
			
			$url_get = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wxuserData['appid'].'&secret='.$wxuserData['appsecret'];
			$json = json_decode($this->curlGet($url_get));

			if($json->errmsg){
				$this->error('获取access_token发生错误：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg.'<br><br>请检查appid和appsecret填写是否正确');
			}

			$this->access_token = $json->access_token;
			//将access_token写入wxuser表中
			$data = array();
			$data['access_token'] = $this->access_token;
			$data['access_token_createtime'] = time();
			$wxuserObj->where($where)->save($data);			
		}

	}

	public function index(){
	
		
		$this->display();
		
	}

/**
 * 更新关注者列表
 */
	public function updateWecha(){
		$this->get_access_token();
		//获取关注者列表
		$url_get = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token;
		$userList = json_decode($this->https_request($url_get));
		if($userList->errmsg){
			$info['info'] = '获取关注者列表发生错误：错误代码：'.$userList->errcode.',微信返回错误信息：'.$userList->errmsg;
		}
		$wechaUser = M('WechaUser');
		//首先清空列表
		$wechaUser->where(array('token'=>session('token')))->delete();
		$flag = true;
		$openidArr = $userList->data->openid;
		
		$touser = '';
		foreach ($openidArr as $key => $value) {
			$data = array();
			$data['token'] = session('token');
			$data['wecha_id'] = $value;

			$touser .= '"'.$value.'",';
			$wechaUser->create($data);
			if($wechaUser->add()){
				$flag = true;
			}else{
				$flag = false;
				break;
			}
		}
		$touser = rtrim($touser,',');
		if(!$flag){
			$this->error('更新失败');	
		}

		//文本
		$type = I('post.type');
		switch ($type) {
					case 'text':
						$content = '"content":"'.I('post.text_content').'"';
						break;

					case 'mpnews':

						$content = '"media_id":"'.$this->upload_mpnews().'"';
						break;
					
					default:
						# code...
						break;
				}		

		$data = '{     
				    "touser":['.$touser.'],
				    "'.$type.'":{           
				           '.$content.'            
				           },     
				    "msgtype":"'.$type.'"
				}';
		
		$url_post = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".$this->access_token;
		$res = json_decode($this->https_request($url_post, $data));

		if($res->errcode === 0){
			$this->success('群发操作成功');
		}
	}

	/**
	 * 上传缩略图
	 */
	public function upload_thumb(){
		$type = 'thumb';
		//如果存在缩略图，则不用再次上传
		$mediaObj = M('Media');
		$where = array();
		$where['token'] = session('token');
		$where['type'] = $type;
		$where['url'] = I('post.pic');
		//media_id最多保存3天，3天后自动删除
		$where['createtime'] = array('lt',time()-3*24*3600);
		$data = array();
		$data = $mediaObj->where($where)->find();
		if($data){
			return $data['media_id'];
		}else{
			$this->get_access_token();

			$filepath = dirname(dirname(dirname(dirname(__FILE__)))).I('post.pic');
			$filedata = array('media'=>'@'.$filepath);
			$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->access_token.'&type='.$type;
			$result = json_decode($this->https_request($url, $filedata));

			if($result->errmsg){
				$this->error('上传缩略图出错：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg);
			}else{
				$mediaObj = M('Media');
				$data = array();
				$data['token'] = session('token');
				$data['type'] = $type;
				$data['url'] = I('post.pic');
				$data['media_id'] = $result->thumb_media_id;
				$data['createtime'] = time();
				$mediaObj->create($data);
				$mediaObj->add();
				return $result->thumb_media_id;
			}			
		}

	}


	private function upload_mpnews(){
		$this->get_access_token();

		//上传图文
		$content = I('post.content');


			$newsdata[] = array(
				'thumb_media_id'=>$this->upload_thumb(),
				'author'=>'',
				'title'=>'微信公众平台开发',
				'content_source_url'=>'fm.baidu.com',
				'content'=>$content,
				'digest'=>'简介',
				'show_cover_pic'=>'0'
				);
			
			$newsdata = json_encode($newsdata,JSON_UNESCAPED_UNICODE);
			$newsdata = '{"articles":'.$newsdata.'}';
			$newsurl = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.$this->access_token;
			$result = json_decode($this->https_request($newsurl, $newsdata));

			if($result->errmsg){
				$this->error('上传图文消息出错：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg);
			}else{
				return $result->media_id;
			}
	}



}