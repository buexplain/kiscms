<?php
namespace Home\Model;
use Common\Model\BaseModel;
class DocExtValueModel extends BaseModel{
    /**
     * 根据文档id获取扩展值信息
     */
    public function getValueByDocId($doc_id) {
        $tmp = $this->where(array('doc_id'=>$doc_id))->field('doc_ext_field_id,value')->select();
        $result = array();
        if($tmp) {
            $DocExtField = D('DocExtField');
            foreach ($tmp as $key => $value) {
                $tmp2 = $DocExtField->get($value['doc_ext_field_id']);
                $tmp3 = array(
                    'field_name'=>$tmp2['field_name'],
                    'field_desc'=>$tmp2['field_desc'],
                    'form_type'=>$tmp2['form_type'],
                );
                $tmp3['form_value_def'] = $DocExtField->getFormValue($tmp2['form_value'],$value['value'],$tmp2['form_type']);
                $result[] = $tmp3;
            }
        }
        return $result;
    }
}