<?php
namespace Home\Model;
use Common\Model\BaseModel;
class DocCategoryModel extends BaseModel{
    private $expire = 86400;
    public function getTopCategory() {
        $cacheKey = 'DocCategory-getTopCategory';
        $result = S($cacheKey);
        if(empty($result)) {
            $result = $this->where(array('pid'=>0))->order('sort asc')->select();
            S($cacheKey,$result,$this->expire);
        }
        return $result;
    }
    public function getSonCategory($cid) {
        $cacheKey = "DocCategory-getSonCategory-pid-{$cid}";
        $result = S($cacheKey);
        if(empty($result)) {
            $result = $this->where(array('pid'=>$cid))->order('sort asc')->select();
            S($cacheKey,$result,$this->expire);
        }
        return $result;
    }
    public function getCnameByCid($cid) {
        $cacheKey = "DocCategory-getCnameByCid-pid-{$cid}";
        $result = S($cacheKey);
        if(empty($result)) {
            $result = $this->where(array('cid'=>$cid))->getField('cname');
            S($cacheKey,$result,$this->expire);
        }
        return $result;
    }
}