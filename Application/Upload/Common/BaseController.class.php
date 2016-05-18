<?php
namespace Upload\Common;
use Think\Controller;
use \Org\Tool\Tool;
/**
 * 后台基础类
 * @author buexplain
 */
class BaseController extends Controller{
    protected $token = 'uploadToken'; //表单令牌key
    protected $tokenNum = 'uploadTokenNmu'; //表单令牌使用次数
    protected $tokenTime = 3600; //表单令牌存在时间
    public function _initialize(){
        $this->crossDomain();
        $this->initTokenKey();
        $this->isLogin();
    }
    /**
     * 判断是否登录
     * 静态服务器上如果要判断是否后台登录
     * 则需要查库一次
     */
    private function isLogin() {
        if($this->getUid() <= 0) {
            $this->ajaxReturn($this->ajaxData(1,'请先登录！',''));
        }
    }
    /**
     * 初始化令牌key值
     */
    private function initTokenKey() {
        $uid = $this->getUid();
        $this->token .= '-'.$uid;
        $this->tokenNum .= '-'.$uid;
    }
    /**
     * 获取uid
     */
    protected function getUid() {
        static $uid;
        if(is_null($uid)) {
            $uid = session(C('USER_AUTH_KEY'));
            if(empty($uid)) {
                $uid = I('post.uid',0,'intval'); //此处的uid是上传表单提供的
            }
        }
        return $uid;
    }
    /**
     * 输出跨域允许头
     */
    protected function crossDomain() {
        $fileCrossDomain = C('fileCrossDomain');
        if(!empty($fileCrossDomain)) {
            header("Access-Control-Allow-Origin: {$fileCrossDomain}");
            header("Access-Control-Allow-Credentials:true");
        }
        if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
            exit;
        }
    }
    /**
     * 生成令牌
     * 如果是跨域上传，那么建议将令牌放入memcache
     */
    protected function createToken() {
        $token = md5(time().range(1, 9999));
        S($this->token,$token,$this->tokenTime);
        return $token;
    }
    /**
     * 读取令牌
     */
    protected function getToken() {
        return S($this->token);
    }
    /**
     * 销毁令牌
     */
    protected function destroyToken() {
        S($this->token,null);
    }
     /**
     * 登记令牌使用次数
     */
    protected function setTokenNum($num,$token) {
        if(!empty($token)) {
            S($this->tokenNum,$num,$this->tokenTime);
        }
    }
    /**
     * 读取令牌使用次数
     */
    protected function getTokenNum() {
        return S($this->tokenNum);
    }
    /**
     * 销毁令牌使用次数
     */
    protected function destroyTokenNum() {
        S($this->tokenNum,null);
    }
    /**
     * 验证表单令牌
     */
    protected function verifyToken($str) {
        $token = $this->getToken();
        $tokenNum = $this->getTokenNum();
        if(!empty($token) && $token == $str && $tokenNum > 0) {
            $tokenNum -= 1;
            if($tokenNum > 0) {
                $this->setTokenNum($tokenNum,$token);
            }else{
                $this->destroyTokenNum();
                $this->destroyToken();
            }
            return true;
        }
        return false;
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
    protected function assignPage(\Think\Page $page,$pageSize) {
        $this->assign('pageSize',$pageSize);
        $this->assign('page',$page->show());
        $this->assign('totalrows',$page->totalRows);
        $html = '';
        $pageSizeArr = C('pageSizeArr');
        foreach ($pageSizeArr as $key => $value) {
            if($value == $pageSize) {
                $html .= '<option class="hand" selected="selected" value="'.$key.'">'.$value.'页</option>';
            }else{
                $html .= '<option class="hand" value="'.$key.'">'.$value.'页</option>';
            }
        }
        $this->assign('pageSize_html',$html);
    }
}