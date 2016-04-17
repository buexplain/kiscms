<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocCategoryRelationModel extends BaseModel{
    /**
    * 根据 doc_id 查找 文档的分类id
    */
    public function getCidByDocId($doc_id) {
		$where = array('doc_id'=>$doc_id);
		$result = $this->where($where)->field('cid')->select();
        $cid = array();
        if($result) {
            foreach ($result as $key => $value) {
                $cid[$value['cid']] = $value['cid'];
            }
        }
        return $cid;
    }
    /**
     * 根据doc_id删除cid
     */
    public function delCidByDocId($doc_id) {
        return $this->where(array('doc_id'=>$doc_id))->delete();
    }
    /**
     * 根据cid删除doc_id
     */
    public function delDocIdByCid($cid) {
        return $this->where(array('cid'=>$cid))->delete();
    }
    /**
     * 根据 doc_id 设置 cid
     */
    public function setCidByDocId($doc_id,$cid) {
        $result = $this->delCidByDocId($doc_id);
        if($result !== false) {
            $cid = (array) $cid;
            $data = array();
            foreach ($cid as $key => $value) {
                $data[] = array('doc_id'=>$doc_id,'cid'=>$value);
            }
            $result = $this->addAll($data);
        }
        if($result !== false) return true;
        return false;
    }
}