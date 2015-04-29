<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use OT\File;

/**
 * 在线更新
 * @author huajie <banhuajie@163.com>
 */
class UpdateController extends AdminController{
    private $params;
    
    public function _initialize(){
        parent::_initialize();
        $this->params = array(
            'version' => C('wxbuluo_version'),
            'domain'  => $_SERVER['HTTP_HOST'],            
        );
    }
	/**
	 * 初始化页面
	 * @author huajie <banhuajie@163.com>
	 */
	public function index(){
		$this->meta_title = '在线更新';

		//检查新版本
		$version = $this->checkVersion();
		if(!empty($version['errcode']) && $version['errcode'] === '10001'){
		    
		    header('location:http://weixin.wxbuluo.com');
		    exit;		    
		}else{
		    
		    $this->assign('version',$version);
		}

		$this->assign('wxbuluo_version',C('wxbuluo_version'));
		//在线更新
		if (IS_POST) {
		    $this->display();
			$this->update($version);
		}else{
		    $this->display();
		}
			
	}

	/**
	 * 检查新版本
	 * @author huajie <banhuajie@163.com>
	 */
	public function checkVersion(){
		if(extension_loaded('curl')){
			$url = 'http://update.wxbuluo.com/index.php?m=home&c=index&a=check_version';
			
			$vars = http_build_query($this->params);
			//获取版本数据
			$data = json_decode($this->getRemoteUrl($url, 'post', $vars),TRUE);
			if(!empty($data)){
			    			
				return $data;
			}else{
				return false;
			}
		}else{
			$this->error('请配置支持curl');
		}
	}

	/**
	 * 在线更新
	 * @author huajie <banhuajie@163.com>
	 */
	private function update($version){
		//PclZip类库不支持命名空间
		import('OT/PclZip');

		$date  = date('YmdHis');
		$backupFile = I('post.backupfile');
		$backupDatabase = I('post.backupdatabase');
		sleep(1);

		$this->showMsg('系统原始版本:'.C('wxbuluo_version'));
		$this->showMsg('微信部落在线更新日志：');
		$this->showMsg('更新开始时间:'.date('Y-m-d H:i:s'));
		sleep(1);

		/* 建立更新文件夹 */
		$folder = $this->getUpdateFolder();
		File::mk_dir($folder);
		$folder = $folder.'/'.$date;
		File::mk_dir($folder);

		/* 获取更新包 */
		//获取更新包地址
		$updatedUrl = 'http://update.wxbuluo.com/index.php?m=home&c=index&a=getDownloadUrl';
		$updatedUrl = json_decode($this->getRemoteUrl($updatedUrl, 'post', http_build_query($this->params)),true);
		if(empty($updatedUrl)){
			$this->showMsg('未获取到更新包的下载地址', 'error');
			exit;
		}
		//下载并保存
		$this->showMsg('开始获取远程更新包...');
// 		sleep(1);
		
		foreach ($updatedUrl as $k=>$v){
		    $zipPath = $folder.'/update_'.$k.'.zip';
		    $downZip = $this->getRemoteUrl($v, 'post', http_build_query($this->params));
		    if(empty($downZip)){
		        $this->showMsg('下载更新包'.$k.'出错，请重试！', 'error');
		        exit;
		    }
		    File::write_file($zipPath, $downZip);
		    $this->showMsg('获取远程更新包【'.$k.'】成功', 'success');
		    sleep(1);
		    
		    /* 解压缩更新包 */ //TODO: 检查权限
		    $this->showMsg('更新包'.$k.'解压缩...');
		    sleep(1);
		    $zip = new \PclZip($zipPath);
		    $res = $zip->extract(PCLZIP_OPT_PATH,$folder);
		    if($res === 0){
		        $this->showMsg($k.'解压缩失败：'.$zip->errorInfo(true).'------更新终止', 'error');
		        exit;
		    }
		    $this->showMsg('更新包'.$k.'解压缩成功', 'success');
		    sleep(1);
		    /* 更新数据库 */
		    $updatesql = $folder.'/update/update.sql';
		    if(is_file($updatesql))
		    {
		        $this->showMsg('更新数据库开始...');
		        if(file_exists($updatesql))
		        {
		            $Model = M();
		            $sql = File::read_file($updatesql);
		            $Model->execute($sql);
		        }
		        unlink($updatesql);
		        $this->showMsg('更新数据库完毕', 'success');
		    }
		    
		    sleep(1);
		    $fileArr = FILE::get_files($folder.'/update');
            foreach ($fileArr as $v){
                $filename = str_replace($folder.'/update/', '', $v);
                if(file_exists($filename)){
                    @unlink($filename);
                }
            }
            MoveFolderFiles($folder.'/update', './');
		}
		
		FILE::del_dir($this->getUpdateFolder());




		$this->showMsg('##################################################################');
		$this->showMsg('在线更新全部完成！', 'success');
	}

	/**
	 * 获取远程数据
	 * @author huajie <banhuajie@163.com>
	 */
	private function getRemoteUrl($url = '', $method = '', $param = ''){
		$opts = array(
			CURLOPT_TIMEOUT        => 20,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL            => $url,
			CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
		);
		if($method === 'post'){
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = $param;
		}

		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data  = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		return $data;
	}

	/**
	 * 实时显示提示信息
	 * @param  string $msg 提示信息
	 * @param  string $class 输出样式（success:成功，error:失败）
	 * @author huajie <banhuajie@163.com>
	 */
	private function showMsg($msg, $class = ''){
		echo "<script type=\"text/javascript\">showmsg(\"{$msg}\",\"{$class}\")</script>";
		flush();
		ob_flush();
	}

	/**
	 * 生成更新文件夹名
	 * @author huajie <banhuajie@163.com>
	 */
	private function getUpdateFolder(){
		$key = sha1(C('DATA_AUTH_KEY'));
		return 'update_'.$key;
	}
}
