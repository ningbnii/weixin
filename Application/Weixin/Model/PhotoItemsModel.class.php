<?php
namespace Weixin\Model;
use Think\Model;

class PhotoItemsModel extends Model{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_INSERT),
		array('title','require','图片标题',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('pid','require','pid',self::VALUE_VALIDATE,'regex',self::MODEL_INSERT),
		array('pic','require','图片',self::VALUE_VALIDATE,'regex',self::MODEL_INSERT),
		);
	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('updatetime',NOW_TIME,self::MODEL_BOTH),
		);

    protected function getToken(){
    	return session('token');
    }
}