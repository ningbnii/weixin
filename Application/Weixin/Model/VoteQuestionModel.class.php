<?php
namespace Weixin\Model;
use Think\Model;

class VoteQuestionModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_INSERT),
		array('title','require','标题',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('description','require','描述',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('name','require','投票来源',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('questionname','require','投票问题',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('pid','require','pid',self::VALUE_VALIDATE,'regex',self::MODEL_INSERT),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('click','0',self::MODEL_INSERT),
		);

    protected function getToken(){
    	return session('token');
    }
		
}