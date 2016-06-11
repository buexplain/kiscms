<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocDiscussModel extends BaseModel{
    public function get($discuss_id,$field='*') {
        return $this->where(array('discuss_id'=>$discuss_id))->field($field)->find();
    }
}