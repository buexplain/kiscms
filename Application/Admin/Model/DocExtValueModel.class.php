<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocExtValueModel extends BaseModel{
    /**
     * 删除某个文档的某个扩展的数据
     */
    public function delDocExtDataByDocIdDocExtId($doc_id,$doc_ext_id) {
        return $this->where(array('doc_id'=>$doc_id,'doc_ext_id'=>$doc_ext_id))->delete();
    }
    public function getByDocId($doc_id) {
        $tmp = $this->where(array('doc_id'=>$doc_id))->select();
        $result = array();
        if($tmp) {
            foreach ($tmp as $key => $value) {
                if(!isset($result[$value['doc_ext_id']])) $result[$value['doc_ext_id']] = array();
                $result[$value['doc_ext_id']][$value['doc_ext_field_id']] = $value['value']; 
            }
        }
        return $result;
    }
    public function delByDocId($doc_id) {
        return $this->where(array('doc_id'=>$doc_id))->delete();
    }
    public function delByDocExtId($doc_ext_id) {
        return $this->where(array('doc_ext_id'=>$doc_ext_id))->delete();
    }
    public function delByDocExtFieldId($doc_ext_field_id) {
        return $this->where(array('doc_ext_field_id'=>$doc_ext_field_id))->delete();
    }
}