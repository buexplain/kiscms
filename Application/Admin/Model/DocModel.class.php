<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocModel extends BaseModel{
    protected $_validate = array(
        array('title','require','请填写标题',1),
        array('content','require','请填写内容',1),
        array('doc_id','number','id错误',2),
    );
    /**
    * 根据 doc_id 查找 文档
    */
    public function get($mixed,$field='*') {
		$where = array('doc_id'=>$mixed);
		return $this->where($where)->field($field)->find();
    }
}