<?php
namespace Weixin\Model;
use Think\Model;

class MusicModel extends Model{
	/**
	 * 自动验证
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('keyword','require','关键词',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('url','require','音乐外链',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);

	/**
	 * 自动完成
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