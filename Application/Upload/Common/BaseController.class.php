<?php
namespace Upload\Common;
use Think\Controller;
use \Org\Tool\Tool;
/**
 * 后台基础类
 * @author buexplain
 */
class BaseController extends Controller{
    public function _initialize(){
        if(!Tool::isAdminLogin()) {
            die('Please login first');
        }
    }
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
     * 开启事务
     */
    protected function startTrans() {
        M()->startTrans();
    }
    /**
     * 回滚事务
     */
    protected function rollback() {
        M()->rollback();
    }
    /**
     * 提交事务
     */
    protected function commit() {
        M()->commit();
    }
    /**
     * 注入分页
     */
    public function assignPage(\Think\Page $page,$pageSize) {
        $this->assign('pageSize',$pageSize);
        $this->assign('page',$page->show());
        $this->assign('totalrows',$page->totalRows);
        $html = '';
        $pageSizeArr = C('pageSizeArr');
        foreach ($pageSizeArr as $key => $value) {
            if($value == $pageSize) {
                $html .= '<option class="hand" selected="selected" onclick="pageSize('.$key.')" value="'.$value.'">'.$value.'页</option>';
            }else{
                $html .= '<option class="hand" onclick="pageSize('.$key.')" value="'.$value.'">'.$value.'页</option>';
            }
        }
        $this->assign('pageSize_html',$html);
    }
}