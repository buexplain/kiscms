<?php
namespace Home\Model;
use Common\Model\BaseModel;
class DocModel extends BaseModel{
    private $state = 2; //文档已通过状态
    /**
     * 获取文档详情
     */
    public function get($doc_id,$ext=false) {
        $result = $this->where(array('state'=>$this->state,'doc_id'=>$doc_id))->find();
        if(!empty($result)) {
            if(isset($result['content'])) $result['content'] = htmlspecialchars_decode($result['content']);
            if($ext) $result['ext_data'] = $this->getExt($doc_id);
        }
        return $result;
    }
    /**
     * 根据cid获取文档
     */
    public function getDocByCid($cid,$field='',$where=array()) {
        $prefix = C('DB_PREFIX');
        $join = "inner join {$prefix}doc_category_relation on {$prefix}doc_category_relation.`doc_id`={$prefix}doc.`doc_id`";
        if(empty($field)) $field = "{$prefix}doc.*";
        $where["{$prefix}doc.`state`"] = $this->state;
        $where["{$prefix}doc_category_relation.`cid`"] = $cid;
        $result = $this->join($join)->field($field)->where($where)->select();
        if(isset($result[0]['content'])) {
            foreach ($result as $key => $value) {
                $result[$key]['content'] = htmlspecialchars_decode($value['content']);
            }
        }
        return $result;
    }
    /**
     * 获取文档扩展
     */
    public function getExt($doc_id) {
        return D('DocExtValue')->getValueByDocId($doc_id);
    }
    /**
     * 归档
     */
    public function timeFile() {
        return $this->field("from_unixtime(unix_timestamp(createtime),'%Y-%m-%d') as t")->order('createtime desc')->group('t')->select();
    }
}