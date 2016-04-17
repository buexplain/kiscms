<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
use \Think\Upload;
class FileUploadController extends BaseController {
    public function index(){
        $fileKey = I('post.fileKey','file');
        $fileArr = isset($_FILES[$fileKey])?$_FILES[$fileKey]:[];
        if(empty($fileArr)) $this->error('$_FILES key 不存在');
        $folder = $this->getFolder(strtolower(pathinfo($fileArr['name'],PATHINFO_EXTENSION)));
        $upload = new upload();
        $upload->hash = false;
        $upload->exts = $this->getExt();
        $upload->subName = array('date', 'Ymd');
        $upload->rootPath = ENTRY."StaticData/{$folder}/";
        $result = \Org\Tool\Tool::mkdirs($upload->rootPath);
        if($result === false) {
            $this->error('存储路径创建失败!');
        }
        $result = $upload->uploadOne($fileArr);
        if(!$result) {
            $this->error($upload->getError());
        }
        $data = [
            'url'=>C('site.staticUrl')."StaticData/{$folder}/{$result['savepath']}{$result['savename']}",
            'name'=>substr($result['name'],0,0-strlen($result['ext'])-1),
            'ext'=>$result['ext'],
        ];
        $this->success($data);
    }
    public function success($data='',$msg='success') {
        $this->ajaxReturn($this->ajaxData(0,$msg,$data));
    }
    public function error($msg) {
        $this->ajaxReturn($this->ajaxData(1,$msg,''));
    }
    public function getExt() {
        return [
            'gif', 'jpg', 'jpeg', 'png','bmp',
            'swf', 'flv', 'fla',
            'mp3', 'mp4', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb',
            'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'
        ];
    }
    public function getFolder($ext) {
        $image = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'psd'];
        if(in_array($ext,$image)) return 'image';
        $flash = ['swf', 'flv','fla'];
        if(in_array($ext,$flash)) return 'flash';
        $media = ['mp3', 'mp4', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'];
        if(in_array($ext,$media)) return 'media';
        $file = ['htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'];
        if(in_array($ext,$file)) return 'file';
        $office = ['doc', 'docx', 'xls', 'xlsx', 'ppt','pptx'];
        if(in_array($ext,$office)) return $office;
        return 'other';
    }
}