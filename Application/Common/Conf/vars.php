<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 各种状态类变量配置
 * @author buexplain
 * @return array
 */
return array(
	/*列表每页大小*/
	'page_size'=>array(10,15,25,40,60,100),
	/*用户的帐号禁封*/
	'user_ban'=>array(1=>'正常',2=>'禁止',3=>'邮箱未激活'),
	/*用户性别*/
	'user_sex'=>array(1=>'未知',2=>'男',3=>'女'),
	/*用户类型*/
	'user_utype'=>array(1=>'会员',2=>'员工'),
    /*表单类型*/
    'form_type'=>array(
        'select'=>'下拉框',
        'checkbox'=>'多选框',
        'radio'=>'单选框',
        'date'=>'日期框',
        'text'=>'文本框',
        'textarea'=>'文本域',
    ),
    /*文档状态*/
    'doc_state'=>array(1=>'草稿',2=>'已发布'),
    /*登录入口*/
    'sign_api'=>array(1=>'管理后台',2=>'前台登录'),
);