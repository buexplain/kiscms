<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
use Org\Tool\Tool;
class FileFormController extends BaseController {
    public function index(){
        //自定义是否允许选择多个文件
        $multiple = I('get.multiple',1,'intval');
        if($multiple) {
            $multiple = 'true';
        }else{
            $multiple = 'false';
        }
        $this->assign('multiple',$multiple);
        //自定义回调函数
        $callBack = I('get.callBack','callBackUpload');
        $this->assign('callBack',$callBack);
        //自定义上传文件限制大小 单位字节
        $fileSingleSizeLimit = I('get.fileSingleSizeLimit','');
        $this->assign('fileSingleSizeLimit',$this->computingSize($fileSingleSizeLimit));
        //自定义选择上传类型
        $extensions = I('get.extensions','');
        $this->assign('extensions',$extensions);
        //上传接口地址
        $this->assign('fileUploadUrl',C('site.fileUploadUrl'));
        //判断文件是否存在的接口地址
        $this->assign('fileHasUrl',C('site.fileHasUrl'));
        $this->display();
    }
    /**
     * 单个文件上传的限制
     */
    private function computingSize($customSize) {
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');

        if($uploadMaxFilesize === false || $postMaxSize === false) return $customSize;

        $uploadMaxFilesizeByte = Tool::parseSize($uploadMaxFilesize);
        $postMaxSizeByte = Tool::parseSize($postMaxSize);

        $maxSizeByte = $uploadMaxFilesizeByte > $postMaxSizeByte ? $postMaxSizeByte : $uploadMaxFilesizeByte;

        if(empty($customSize)) return Tool::formatSize($maxSizeByte);

        $customSizeByte = Tool::parseSize($customSize);

        if($customSizeByte > $maxSizeByte) {
            return Tool::formatSize($maxSizeByte);
        }else{
            return $customSize;
        }
    }
}