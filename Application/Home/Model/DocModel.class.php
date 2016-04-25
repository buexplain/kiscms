<?php
namespace Home\Model;
use Common\Model\BaseModel;
class DocModel extends BaseModel{
    private $expire = 86400;
    private $state = 2; //文档已通过状态
    /**
     * 获取首页列表
     */
    public function getIndexList() {
        $cacheKey = 'DocCategory-getIndexList';
        $result = S($cacheKey);
        if(empty($result)) {
            $field = 'doc_id,title,createtime';
            $result = D('Doc')->field($field)->order('createtime desc')->where(array('state'=>$this->state))->limit(0,C('site.page_size'))->select();
            S($cacheKey,$result,$this->expire/8);
        }
        return $result;
    }
    /**
     * 获取文档详情
     */
    public function get($doc_id,$ext=false) {
        $result = $this->where(array('state'=>$this->state,'doc_id'=>$doc_id))->find();
        if(!empty($result)) {
            if(isset($result['content'])) $result['content'] = htmlspecialchars_decode($result['content']);
            if($ext) {
                $result['ext_data'] = $this->getExt($doc_id);
            }
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
        $cacheKey = "Doc-timeFile";
        $result = S($cacheKey);
        if(empty($result)) {
            $result = $this->field("from_unixtime(unix_timestamp(createtime),'%Y-%m-%d') as t")->order('createtime desc')->group('t')->select();
            S($cacheKey,$result,$this->expire*20);
        }
        return $result;
    }
    /**
     * 更新浏览数
     */
    public function upViews($doc_id) {
        return $this->where(array('doc_id'=>$doc_id))->setInc('views',1);
    }
}