<?php
/**
 * 检查是否登录了系统
 */
function check_login(){
    $user = $_SESSION['onethink_home']['user_auth'];
    if (empty($user)) {
        return 0;
    } else {
        return $_SESSION['onethink_home']['user_auth_sign'] == data_auth_sign($user) ? $user['uid'] : 0;
    }
}