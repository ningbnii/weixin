<?php
namespace Weixin\Model;
use Think\Model;

class DiymenModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('keyword','require','关键词',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('title','require','菜单名字',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('updatetime',NOW_TIME,self::MODEL_BOTH),
        array('path','getPath',self::MODEL_BOTH,'callback'),
		);


    protected function getToken(){
    	return session('token');
    }

    protected function getPath(){
    	if(I('request.fid') != 0){
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