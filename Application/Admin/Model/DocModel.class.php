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
    /**
     * 更新文档状态
     */
    public function setState($doc_id) {
        $where = array('doc_id'=>$doc_id);
        $state = $this->where($where)->getField('state');
        if(empty($state)) return false;
        $data = array();
        if($state == 1) {
            $data['state'] = 2;
            $data['pushtime'] = date('Y-m-d H:i:s');
        }else{
            $data['state'] = 1;
        }
        return $this->where($where)->data($data)->save();
    }
}