<?php
namespace Weixin\Model;
use Think\Model\RelationModel;

class ShopProductsModel extends RelationModel{
	/**
	 * 自动验证规则
	 */
	protected $_validate = array(
		array('token','require','token',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('classid','require','产品类别id',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('name','require','产品名称',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('number','require','产品数量',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('price','require','产品价格',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('pic','require','产品缩略图',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		array('info','require','产品内容描述',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
		);

	/**
	 * 自动完成规则
	 */
	protected $_auto = array(
		array('token','getToken',self::MODEL_INSERT,'callback'),
		array('createtime',NOW_TIME,self::MODEL_INSERT),
		array('updatetime',NOW_TIME,self::MODEL_BOTH),
		);

	/**
	 * 关联模型
	 */
	protected $_link = array(
		'shop_classify'=>array(
			'mapping_type'=>self::BELONGS_TO,
			'class_name'=>'shop_classify',
			'maaping_name'=>'shop_classify',
			'foreign_key'=>'classid',
			'mapping_fields'=>array('name'),
			'as_fields'=>'name:class_name',
			),
		);

	protected function getToken(){
		return session('token');
	}
}