<?php

/**
 * 检测前台用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function check_login(){
	$user = $_SESSION['onethink_home']['user_auth'];
	if (empty($user)) {
		return 0;
	} else {
		return $_SESSION['onethink_home']['user_auth_sign'] == data_auth_sign($user) ? $user['uid'] : 0;
	}
}