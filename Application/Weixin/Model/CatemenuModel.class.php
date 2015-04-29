<?php
namespace Weixin\Model;
use Think\Model;

class CatemenuModel extends Model{
	 /**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('updatetime',NOW_TIME,self::MODEL_BOTH),
        array('fid','getFid',self::MODEL_BOTH,'callback'),
		array('path','getPath',self::MODEL_BOTH,'callback'),
		);


    protected function getToken(){
    	return session('token');
    }
    protected function getFid(){
    	if(I('request.fid')){
    		$fid = I('request.fid');
    	}else{
    		$fid = 0;
    	}
    	return $fid;
    }

    protected function getPath(){
    	if(I('request.fid')){
    		$where = array();
    		$where['id'] = I('request.fid');
    		$data = $this->where($where)->find();
    		$path = $data['path'].'-'.I('request.fid');
    	}else{
    		$path = 0;
    	}
    	return $path;
    }
    
}