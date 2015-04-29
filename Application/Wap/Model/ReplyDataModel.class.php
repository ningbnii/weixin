<?php
namespace Wap\Model;
use Think\Model;

class ReplyDataModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('wecha_id','require','wecha_id不能为空',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);

	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
		array('wecha_id','getWechaId',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('updatetime',NOW_TIME,self::MODEL_BOTH),
		);
	
    protected function getToken(){
    	return session('token');
    }	
    protected function getWechaId(){
    	return session('wecha_id');
    }
}