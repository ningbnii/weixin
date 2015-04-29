<?php
namespace Weixin\Model;
use Think\Model;

class KeywordModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('keyword','require','关键词',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('pid','require','外联id',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('module','require','模块名称',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
		array('type','getType',self::MODEL_BOTH,'callback'),
		);


    protected function getToken(){
    	return session('token');
    }

    protected function getType(){
    	if(I('post.type')){
    		return I('post.type');
    	}else{
    		return 1;
    	}
    }
}