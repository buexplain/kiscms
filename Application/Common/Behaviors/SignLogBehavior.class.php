<?php
namespace Common\Behaviors;
use \Think\Behavior;
/**
 * 用户登录行为扩展：登录日志写入
 */
class SignLogBehavior extends Behavior {
    // 行为扩展的执行入口必须是run
    public function run(&$param) {
        $sign_api = C('sign_api');
        if(isset($sign_api[$param])) {
            $data = array(
                'uid'=>session(C('USER_AUTH_KEY')),
                'sign_ip'=>get_client_ip(),
                'sign_time'=>date('Y-m-d H:i:s'),
                'sign_api'=>$param,
            );
            M('Usign')->data($data)->add();
        }
    }
}