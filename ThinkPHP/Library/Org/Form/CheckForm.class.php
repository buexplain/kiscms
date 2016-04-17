<?php
namespace Org\Form;
/**
 * 表单验证类
 * @author buexplain
 */
class CheckForm{
    /**
     * 验证手机号码
     */
    public static function mobile($str){
        $str = trim($str);
        $pattern = "~^1[34587]\d{9}$~";
        if(preg_match($pattern,$str)){
            return  $str;
        } else {
            return false;
        }
    }
    /**
     * 验证邮箱
     */
    public static function email($str) {
        $str = trim($str);
        $pattern = "~^[\w\.]+@[\w]+\.[a-zA-Z\_]+$~";
        if(preg_match($pattern,$str)){
            return  $str;
        } else {
            return false;
        }
    }
    /**
     * 验证密码
     */
    public static function passwd($str) {
        return strlen($str) > 5;
    }
    
}