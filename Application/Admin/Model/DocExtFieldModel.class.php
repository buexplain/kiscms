<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocExtFieldModel extends BaseModel{
    /**
    * 根据 doc_ext_field_id 查找字段
    */
    public function get($mixed,$field='*') {
		$where = array('doc_ext_field_id'=>$mixed);
		return $this->where($where)->field($field)->find();
    }
    /**
     * 根据 doc_ext_id 查找字段
     */
    public function getBydocExtId($doc_ext_id) {
        return $this->where(array('doc_ext_id'=>$doc_ext_id))->order('sort asc')->select();
    }
    /**
     * 根据 doc_ext_id 删除字段
     */
    public function delBydocExtId($doc_ext_id) {
        return $this->where(array('doc_ext_id'=>$doc_ext_id))->delete();
    }
}