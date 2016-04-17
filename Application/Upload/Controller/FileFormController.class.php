<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
class FileFormController extends BaseController {
    public function index(){
        //自定义是否允许选择多个文件
        $moreFile = I('get.moreFile',1,'intval');
        if($moreFile) {
            $moreFile = 'true';
        }else{
            $moreFile = 'false';
        }
        $this->assign('moreFile',$moreFile);
        //自定义回调函数
        $callBack = I('get.callBack','callBackPlupload');
        $this->assign('callBack',$callBack);
        //自定义上传文件限制大小
        $sizeFile = I('get.sizeFile','');
        $this->assign('sizeFile',$sizeFile);
        //自定义选择上传类型
        $ext = I('get.ext','');
        $this->assign('ext',$ext);
        //上传接口地址
        $this->assign('fileUploadUrl',C('site.fileUploadUrl'));
        $this->display();
    }
}