<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class UinfoModel extends BaseModel{
    protected $_validate = array(
        array('mobile',"isMobile",'请填写正确格式的手机号码',2,'callback'),
        array('email',"isEmail",'请填写正确格式的邮箱地址',2,'callback'),
        array('sex',array(1,2,3),'请选择性别',1,'in'),
        array('uid',"gtZeroInt",'uid错误',1,'callback'),
    );
    /**
    * 根据邮箱 或 手机号码 或 uid 查找用户
    */
    public function get($mixed,$field='*') {
		$where = array('uid'=>$mixed);
		return $this->where($where)->field($field)->find();
    }
    /**
     * 判断邮箱号是否重复
     */
    public function emailIsNotRepeat($email,$uid=0) {
        $where = array('email'=>$email);
        if($uid > 0) $where['uid'] = array('NEQ',$uid);
        $uid = $this->where($where)->getField('uid');
        if(empty($uid)) return true;
        return false;
    }
    /**
     * 判断手机号是否重复
     */
    public function mobileIsNotRepeat($mobile,$uid=0) {
        $where = array('mobile'=>$mobile);
        if($uid > 0) $where['uid'] = array('NEQ',$uid);
        $uid = $this->where($where)->getField('uid');
        if(empty($uid)) return true;
        return false;
    }
    /**
     * 根据uid获取用户昵称
     */
    public function getUinfoByUid($uid) {
        static $uinfoArr;
        if(!isset($uinfoArr[$uid])) {
            $uinfoArr[$uid] = $this->get($uid);
        }
        return $uinfoArr[$uid];
    }
    /**
     * 根据真实姓名获取uid
     */
    public function getUidByRealname($realname) {
        $tmp = $this->where(array('realname'=>$realname))->field('uid')->find();
        if(empty($tmp)) $uid = 0;
        $uid = $tmp['uid'];
        return $uid;
    }
}