<?php
namespace Upload\Model;
use Think\Model;
/**
 * 文件模型
 * @author buexplain
 */
class FileModel extends Model{
    public function getFileByMD5($md5,$field='*') {
        $md5 = (array) $md5;
        return $this->field($field)->where(array('md5'=>array('IN',$md5)))->select();
    }
}