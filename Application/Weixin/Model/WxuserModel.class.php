<?php 
namespace Weixin\Model;
use Think\Model;

class WxuserModel extends Model{
    /* 自动验证规则 */
    protected $_validate = array(
    	array('wxname', 'require', '公众号名称', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
    	array('wxid', 'require', '公众号原始ID', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
    	array('weixin', 'require', '微信号', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),	
    );
    
    /* 自动完成规则 */
    protected $_auto = array(
    		array('token', 'getToken', self::MODEL_INSERT, 'callback'),
    		array('uid', 'getUid', self::MODEL_INSERT, 'callback'),
            array('createtime',NOW_TIME,self::MODEL_INSERT),
            array('updatetime',NOW_TIME,self::MODEL_BOTH),
    );
    
    protected function getToken(){
    	$randLength = 6;
    	$chars = 'abcdefghijklmnopqrstuvwxyz';
    	$len = strlen($chars);
    	$randStr = '';
    	for($i=0;$i<$randLength;$i++){
    		$randStr .= $chars[rand(0, $len-1)];
    	}
    	return $randStr.time();
    }
    
    protected function getUid(){
    	return $_SESSION['onethink_home']['user_auth']['uid'];
    }
}
?>