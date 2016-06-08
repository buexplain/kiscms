<?php
namespace Home\Model;
use Common\Model\BaseModel;
class UinfoModel extends BaseModel{
    private $uinfo = array();
    public function getUinfoByUid($uid) {
        if(!isset($uinfo[$uid])) {
            $tmp = $this->where(array('uid'=>$uid))->find();
            $this->uinfo[$uid] = array();
            if(!empty($tmp)) {
                $this->uinfo[$uid] = $tmp;
            }
        }
        return $this->uinfo[$uid];
    }
}