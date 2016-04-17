<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class DocCategoryModel extends BaseModel{
    protected $_validate = array(
        array('cname','require','请填写分类名',1),
        array('cid','number','id错误',1),
        array('pid','number','父级id错误',1),
        array('sort','number','请填写排序整数',1),
    );
    /**
    * 根据 cid查找分类
    */
    public function get($mixed,$field='*') {
		$where = array('cid'=>$mixed);
		return $this->where($where)->field($field)->find();
    }
    /**
     * 获取所有分类
     */
    public function getAll() {
        static $result;
        if(!empty($result)) return $result;
        $tmp = $this->order('sort asc')->select();
        foreach ($tmp as $key => $value) {
            $result[$value['cid']] = $value;
        }
        return $result;
    }
    /**
     * 根据cid获取cname
     */
    public function getCnameByCid($cid) {
        $result = $this->getAll();
        $cid = (array) $cid;
        $cname = array();
        foreach ($cid as $key => $value) {
            if(isset($result[$value])) $cname[] = $result[$value]['cname'];
        }
        return $cname;
    }
    /**
     * 根据cid获取所有子分类
     */
    public function son($cid) {
        return $this->where(array('pid'=>$cid))->select();
    }
    /**
     * 根据pid获取当前分类的深度
     */
    public function getDepthByPid($pid) {
        if(empty($pid)) return 1;
        return $this->where(array('cid'=>$pid))->getField('depth') + 1;
    }
    /**
     * 获取ztree的数据
     */
    public function getCategoryZtree($cid) {
        $cid = (array) $cid;
        $result = $this->getAll();
        $new_result = array();
        foreach ($result as $key => $value) {
            $tmp = array(
                'id'=>$value['cid'],
                'pId'=>$value['pid'],'name'=>$value['cname'],
                'open'=>'true',
                'checked'=> (isset($cid[$value['cid']]) ? 'true' : 'false'),
                'depth'=>$value['depth'],
            );

            $new_result[] = $tmp;
        }
        return json_encode($new_result);
    }
}