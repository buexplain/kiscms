<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
use \Think\Upload;
class FileUploadController extends BaseController {
    /**
     * 上传文件
     */
    public function index(){
        $fileVal = I('post.fileVal','file');
        $fileArr = isset($_FILES[$fileVal])?$_FILES[$fileVal]:array();
        if(empty($fileArr)) $this->error('$_FILES key 不存在');
        $folder = $this->getFolder(strtolower(pathinfo($fileArr['name'],PATHINFO_EXTENSION)));
        $upload = new upload();
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

        $dir = "StaticData/{$folder}/{$result['savepath']}{$result['savename']}";

        $return = array(
            'url'=>C('site.staticUrl').$dir,
            'oname'=>substr($result['name'],0,0-strlen($result['ext'])-1),
            'ext'=>$result['ext'],
        );

        $fileArr = array(
            'md5'=>$result['md5'],
            'oname'=>$return['oname'],
            'dir'=>$dir,
            'ext'=>$result['ext'],
            'size'=>$result['size'],
            'createtime'=>date('Y-m-d H:i:s')
        );

        $result = D('File')->data($fileArr)->add();
        if(empty($result)) {
            unlink($dir);
            $this->error('Register file failed');
        }

        $this->success($return);
    }
    /**
     * 判断文件是否存在
     */
    public function has() {
        $md5Str = I('post.md5Str','');
        $md5Str = explode(',',$md5Str);
        $md5Arr = array();
        foreach ($md5Str as $key => $value) {
            if(strlen($value) == 32) $md5Arr[] = $value;
        }

        if(count($md5Arr) == 0) {
            $this->error('md5 empty');
        }

        $field = 'md5,dir as url,oname,ext';
        $result = D('File')->getFileByMD5($md5Arr,$field);

        foreach ($result as $key => $value) {
            $result[$value['md5']] = $value;
            unset($result[$key]);
        }
        $staticUrl = C('site.staticUrl');
        foreach ($md5Arr as $key => $value) {
            if(isset($result[$value])) {
                $md5Arr[$key] = array();
                $md5Arr[$key]['md5'] = $value;
                $md5Arr[$key]['data'] = $result[$value];
                $md5Arr[$key]['data']['url'] = $staticUrl.$md5Arr[$key]['data']['url'];
                unset($md5Arr[$key]['data']['md5']);
            }else{
                unset($md5Arr[$key]);
            }
        }
        $this->success($md5Arr);
    }
    protected function success($data='',$msg='success') {
        $this->ajaxReturn($this->ajaxData(0,$msg,$data));
    }
    protected function error($msg) {
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