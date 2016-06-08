<?php
namespace Admin\Common;
use Think\Model;
use \Org\Form\CheckForm;
/**
 * 后台基础模型
 * @author buexplain
 */
class BaseModel extends Model{
    /**
     * 验证大于零的整型数
     */
    public function gtZeroInt($data) {
        $pattern = "~^[1-9]{1,1}[0-9]*$~";
        if(preg_match($pattern,$data)) return true;
        return false;
    }
    /**
     * 验证大于等于零的整型数
     */
    public function egtZeroInt($data) {
        $pattern = "~^[1-9]{1,1}[0-9]*$~";
        if(preg_match($pattern,$data)) return true;
        return false;
    }
    /**
     * 验证是否为邮箱
     */
    public function isEmail($data) {
        return CheckForm::email($data);
    }
    /**
     * 验证是否为手机号
     */
    public function isMobile($data) {
        return CheckForm::mobile($data);
    }
}