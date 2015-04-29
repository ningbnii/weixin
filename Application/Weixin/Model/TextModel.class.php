<?php
namespace Weixin\Model;
use Think\Model;

class TextModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('uid','require','用户id',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('keyword','require','关键词',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('text','require','返回内容',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('uid','getUid',self::MODEL_INSERT,'callback'),
		array('token','getToken',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('updatetime',NOW_TIME,self::MODEL_BOTH),
		);


    protected function getToken(){
    	return session('token');
    }
    
    protected function getUid(){
    	return $_SESSION['onethink_home']['user_auth']['uid'];
    }    
}