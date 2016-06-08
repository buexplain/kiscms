<?php
namespace Common\Controller;
use Think\Controller;
/**
 * 前台基础控制器
 * @author buexplain
 */
class BaseController extends Controller{
    /**
     * 操作错误跳转的快捷方法
     */
    protected function error($msg='error',$data='',$code=1) {
        if(IS_AJAX) {
            $tmp = $this->ajaxData($code,$msg,$data);
            $this->ajaxReturn($tmp);
        }
        parent::error($msg,$data);
    }
    /**
     * 操作成功跳转的快捷方法
     * 第三个参数不用 无效
     */
    protected function success($data='',$msg='success',$void='') {
        if(IS_AJAX) {
            $tmp = $this->ajaxData(0,$msg,$data);
            $this->ajaxReturn($tmp);
        }
        parent::success($msg,$data);
    }
    /**
     * 封装ajax返回格式
     */
    protected function ajaxData($code,$msg,$data) {
        return array(
            'code'=> $code,
            'msg' => $msg,
            'data'=> $data
        );
    }
    /**
     * 注入分页
     */
    public function assignPage(\Think\Page $page,$pageSize) {
        $this->assign('page',$page->show());
        $this->assign('totalrows',$page->totalRows);
    }
    /**
     * 手动开启session
     */
    protected function sessionStart() {
        C('SESSION_AUTO_START',true);
        session(array());
    }
}