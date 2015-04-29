<?php
namespace Weixin\Model;
use Think\Model;

class VoteModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('keyword','require','关键词',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('title','require','标题',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('info','require','描述',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('starttime','require','投票开始时间',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('endtime','require','投票结束时间',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('starttime','getStarttime',self::MODEL_BOTH,'callback'),
        array('endtime','getEndtime',self::MODEL_BOTH,'callback'),
		);

    protected function getToken(){
    	return session('token');
    }

    protected function getStarttime(){
    	return strtotime(I('post.starttime'));
    }

    protected function getEndtime(){
    	return strtotime(I('post.endtime'));
    }
}