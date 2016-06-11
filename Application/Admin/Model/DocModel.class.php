<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocModel extends BaseModel{
    private $docIDtitleArr = array();
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
     * 根据doc_id获取标题
     */
    public function getTitleByDocID($doc_id) {
        if(!isset($this->docIDtitleArr[$doc_id])) {
            $tmp = $this->where(array('doc_id'=>$doc_id))->getField('title');
            if($tmp) {
                $this->docIDtitleArr[$doc_id] = $tmp;
            }
        }
        return isset($this->docIDtitleArr[$doc_id]) ? $this->docIDtitleArr[$doc_id] : false;
    }
}