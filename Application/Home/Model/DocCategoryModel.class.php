<?php
namespace Home\Model;
use Common\Model\BaseModel;
class DocCategoryModel extends BaseModel{
    public function getTopCategory() {
        return $this->where(array('pid'=>0))->order('sort asc')->select();
    }
    public function getSonCategory($cid) {
        return $this->where(array('pid'=>$cid))->order('sort asc')->select();
    }
    public function getCnameByCid($cid) {
        return $this->where(array('cid'=>$cid))->getField('cname');
    }
}