<?php
namespace Home\Model;
use Common\Model\BaseModel;
use \Org\Tool\Tool;
class DocExtFieldModel extends BaseModel{
    /**
     * 根据主键获取字段信息
     */
    public function get($doc_ext_field_id,$field="*") {
        return $this->field($field)->find($doc_ext_field_id);
        
    }
    /**
     * 取得扩展字段的表单值
     */
    public function getFormValue($form_value,$default_value,$form_type) {
        $result = '';
        switch ($form_type) {
            case 'select':
                $form_value = Tool::parseFormValue($form_value);
                foreach ($form_value as $key => $value) {
                    if($key == $default_value) {
                        $result = $value;
                        break;
                    }
                }
                break;
            case 'checkbox':
                $form_value = Tool::parseFormValue($form_value);
                $default_value = explode(',',$default_value);
                foreach ($form_value as $key => $value) {
                    if(in_array($key, $default_value)) {
                        $result .= ','.$value;
                    }
                }
                break;
            case 'radio':
                $form_value = Tool::parseFormValue($form_value);
                foreach ($form_value as $key => $value) {
                    if($key == $default_value) {
                        $result = $value;
                    }
                }
                break;
            case 'date':
            case 'text':
            case 'textarea':
                $result = $default_value;
                break;
        }
        return $result;
    }
}