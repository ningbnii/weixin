<?php
namespace Weixin\Controller;
use User\Api\UserApi;
/**
 * 
 * @author ning
 *
 */
class IndexController extends WeixinController{
	public function index(){
		$wxuserObj = M('Wxuser');
		$where['uid'] = $_SESSION['onethink_home']['user_auth']['uid'];
		$list = $wxuserObj->where($where)->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$this->display();
	}

/**
 * 删除公众号
 */
/*	public function del(){
		return;
		if(M('Wxuser')->where(array('id'=>I('get.id')))->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}*/
	
	public function insert(){
		
		$wxuserObj = D('Wxuser');
		$wxuserObj->create();
		if($wxuserObj->add()){
			$this->success('添加成功',U('index'));
		}else{
			$this->error('添加失败');
		}
	}

	public function edit(){
		if(IS_POST){
			$wxuserObj = D('Wxuser');
			$wxuserObj->create();
			if($wxuserObj->save()){
				$this->success('修改成功',U('index'));
			}else{
				$this->error('修改失败',U('index'));
			}
		}else{
			$wxuserObj = M('Wxuser');
			$where['id'] = I('get.id');
			$data = $wxuserObj->where($where)->find();
			$this->assign('data',$data);
			$this->display();
		}
	}

	public function viewapi(){
		$wxuserObj = M('Wxuser');
		$where['id'] = I('get.id');
		$data = $wxuserObj->where($where)->find();
		$this->assign('data',$data);
		$this->display();
	}
	
	/**
	 * 修改密码
	 */
	public function changePwd(){
		if(IS_POST){
			$uid = $_SESSION['onethink_home']['user_auth']['uid'];
			$password = I('post.old');
			$repassword = I('post.repassword');
			$data['password'] = I('password');
			empty($password) && $this->error('请输入原密码');
			empty($data['password']) && $this->error('请输入新密码');
			empty($repassword) && $this->error('请输入确认密码');

			if($data['password'] !== $repassword){
				$this->error('您输入的新密码与确认密码不一致');
			}

			$Api = new UserApi();
			$res = $Api->updateInfo($uid, $password, $data);
			if($res['status']){
				$this->success('密码修改成功');
			}else{
				if($res['info'] == -4){
					$this->error('密码不能少于6位');
				}else{
					$this->error($res['info']);
				}
			}
		}else{
			$this->display();
		}
	}
	

}