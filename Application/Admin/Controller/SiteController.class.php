<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
class SiteController extends BaseController {
    public function listCache() {
        $result = $this->del(RUNTIME_PATH);
        if($result) {
            $yesNo = 'success';
        }else{
            $yesNo = 'danger';
        }
        $msg = $yesNo;
        $this->assign('msg',$msg);
        $this->assign('yesNo',$yesNo);
        $this->display();
    }
    /**
     * 递归删除目录
     * @return boolean 成功返回true,失败返回false;
     */
    private function del($file) {
        if (!file_exists($file) && !is_dir($file)) return true;
        if (is_dir($file) && !is_link($file)) {
            foreach(glob($file . '/*') as $sf) {
                    if (!$this->del($sf)) {
                    return false;
                }
            }
            // 删除目录
            return @rmdir($file);
        } else {
            // 删除文件
            return @unlink($file);
        }
    }
}