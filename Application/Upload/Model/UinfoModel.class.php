<?php
namespace Upload\Model;
use Think\Model;
/**
 * 用户模型
 * @author buexplain
 */
class UinfoModel extends Model{
    /**
    * 根据邮箱 或 手机号码 或 uid 查找用户
    */
    public function get($mixed,$field='*') {
        $where = array('uid'=>$mixed);
        return $this->where($where)->field($field)->find();
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