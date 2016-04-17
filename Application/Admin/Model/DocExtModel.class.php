<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocExtModel extends BaseModel{
    protected $_validate = array(
        array('ext_name','require','请填写扩展名',1),
        array('doc_ext_id','number','id错误',2),
        array('sort','number','请填写排序整数',2),
    );
    /**
    * 根据 doc_ext_id 查找 扩展
    */
    public function get($mixed,$field='*') {
		$where = array('doc_ext_id'=>$mixed);
		return $this->where($where)->field($field)->find();
    }
    /**
     * 获取所有的扩展
     */
    public function getAll() {
        return $this->order('sort asc')->field('doc_ext_id,ext_name')->select();
    }
    public function getExtNameByDocExtId($doc_ext_id) {
        return $this->where(array('doc_ext_id'=>$doc_ext_id))->getField('ext_name');
    }
}