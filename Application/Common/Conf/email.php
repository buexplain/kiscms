<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 邮件服务器配置
 * @author buexplain
 * @return array
 */
return array(
    'MAIL_HOST'=>'', //发件服务器
    'MAIL_PORT'=>25,
    'MAIL_USERNAME'=>'',
    'MAIL_PWD'=>'',
    'MAIL_FROM'=>'', //发送者
    'MAIL_SECURE'=>'tls',
);