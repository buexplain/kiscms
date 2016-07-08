<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
use Org\Tool\Tool;
class DocDiscussController extends BaseController {
    private $state = array(0=>'状态',1=>'正常',2=>'删除');
    public function listDocDiscuss() {
        /*关键词检索*/
        $search_arr = array('关键词类型','ID','标题ID','用户ID','评论内容');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        /*时间检索*/
        $search_start_time = I('get.search_start_time','');
        $search_end_time = I('get.search_end_time','');
        $this->assign('search_start_time',$search_start_time);
        $this->assign('search_end_time',$search_end_time);

        /*状态检索*/
        $state_cur = I('get.state_cur',0,'intval');
        $this->assign('state_cur',$state_cur);
        $this->assign('state',$this->state);

        $where = array();
        if($search_type) {
            switch ($search_type) {
                case 1:
                    $where['discuss_id'] = intval($search_keywrod);
                    break;
                case 2:
                    $where['doc_id'] = intval($search_keywrod);
                    break;
                case 3:
                    $where['uid'] = intval($search_keywrod);
                    break;
                case 4:
                    $where['content'] = array('like',"{$search_keywrod}%");
                    break;
            }
        }

        if($search_start_time && $search_end_time) {
            $where['createtime'] = array('between',array($search_start_time,$search_end_time));
        }elseif ($search_start_time) {
            $where['createtime'] = array('egt',$search_start_time);
        }elseif ($search_end_time) {
            $where['createtime'] = array('elt',$search_end_time);
        }

        if($state_cur) {
            $where['state'] = $state_cur;
        }

        $counter = D('DocDiscuss')->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);
        $result  = D('DocDiscuss')->order('createtime desc')->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('DocDiscuss')->getLastSql();
        foreach ($result as $key => &$value) {
            $value['title'] = D('Doc')->getTitleByDocID($value['doc_id']);
            $value['nickname'] = D('Uinfo')->getNicknameByUid($value['uid']);
            $value['stateTxt'] = $this->state[$value['state']];

            $value['handle'] = '<a href="javascript:;" data-url="'.U('DocDiscuss/setState',array('discuss_id'=>$value['discuss_id'],'state'=>$value['state'])).'" class="batch">状态</a>';
            $value['handle'] .= '<a href="'.U('DocDiscuss/addDiscuss',array('discuss_id'=>$value['discuss_id'])).'">回复</a>';
            $value['handle'] .= '<a href="javascript:;" data-url="'.U('DocDiscuss/delDocDiscuss',array('discuss_id'=>$value['discuss_id'])).'" class="batch">删除</a>';
        }

        //dump($result);
        $this->assignPage($page,$pageSize);
        $this->assign('result',$result);

        $this->display();
    }
    /**
     * 逻辑删除
     */
    public function setState() {
        $discuss_id = filterNumStr(I('get.discuss_id'));
        $state = I('get.state',0,'intval');

        if(empty($discuss_id) || !in_array($state,array(1,2))) {
            $this->error('缺省参数');
        }

        if($state == 1) {
            $state = 2;
        }else{
            $state = 1;
        }

        $where = array();
        $where['discuss_id'] = array('IN',$discuss_id);

        $data = array('state'=>$state);

        $this->startTrans();
        $result = D('DocDiscuss')->where($where)->data($data)->save();

        if($result === false) {
            $this->rollback();
            $this->error();
        }

        $this->commit();
        $this->success(U('DocDiscuss/listDocDiscuss'));

    }
    /**
     * 物理删除
     */
    public function delDocDiscuss() {
        $discuss_id = filterNumStr(I('get.discuss_id'));

        $where = array();
        $where['discuss_id'] = array('IN',$discuss_id);

        $this->startTrans();

        $result = D('DocDiscuss')->where($where)->delete();
        if($result === false) {
            $this->rollback();
            $this->error();
        }

        $this->commit();
        $this->success(U('DocDiscuss/listDocDiscuss'));
    }
    /**
     * 回复评论
     */
    public function addDiscuss() {
        $discuss_id = I('discuss_id',0,'intval');
        $discuss = D('DocDiscuss')->get($discuss_id);
        if(empty($discuss)) $this->error('缺省参数');

        if(IS_POST) {
            $content = $this->getDiscuss();
            if(empty($content)) $this->error('请填写评论');

            $data = array();
            $data['uid'] = session(C('USER_AUTH_KEY'));
            $data['doc_id'] = $discuss['doc_id'];
            $data['pid'] = $discuss_id;
            $data['content'] = $content;
            $data['state'] = 1;
            $data['createtime'] = date('Y-m-d H:i:s');

            $this->startTrans();

            $result = D('DocDiscuss')->data($data)->add();
            if(empty($result)) {
                $this->rollback();
                $this->error();
            }

            $this->commit();

            if(C('MAIL_HOST')) {
                $sendEmail = D('Ucenter')->where(array('uid'=>$discuss['uid']))->getField('email');
                $title = D('Doc')->where(array('doc_id'=>$discuss['doc_id']))->getField('title');
                $url = U('Home/Desc/index',array('doc_id'=>$data['doc_id']));
                $parentContent = $discuss['content'];

                $result = $this->sendMail($sendEmail,$title,$url,$content,$parentContent);
            }

            $this->success(U('DocDiscuss/listDocDiscuss'));

        }else{
            $discuss['nickname'] = D('Uinfo')->getNicknameByUid($discuss['uid']);
            $this->assign('discuss',$discuss);
            $this->display();
        }
    }
    /**
     * 获取提交的评论
     */
    private function getDiscuss() {
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $content = Tool::filter($content,'pre');
        $search  = array("\r",'</pre><br>',"<br>\n<pre>");
        $replace = array('<br>','</pre>','<pre>');
        $content = str_replace($search, $replace, $content);
        $pattern = "~<pre>([\w\W]*?)</pre>~";
        $content = preg_replace_callback($pattern,function($matches){
            return str_replace('<br>','',$matches[0]);
        },$content);
        return $content;
    }
    /**
     * 评论成功，发送邮件
     */
    private function sendMail($email,$title,$url,$content,$parentContent) {
        $host = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
        $url = $host.$url;
        if(empty($parentContent)) {
            $subject = ' - 新的评论！';
            $parentContent = '';
        }else{
            $subject = ' - 新的回复！';
            $parentContent = '<blockquote style="COLOR:#000000; BACKGROUND: #efefef;padding:5px;margin:0 5px;">'.$parentContent.'</blockquote>';
        }
        $body = '<div style="margin:0 0 10px 0;"><b><a href="'.$url.'">'.$title.'</a></b></div><div style="margin:0 0 10px 0;">'.$parentContent.'</div><div style="margin:0 0 10px 0;">'.$content.'</div><div><hr color="#b5c4df" size="1" align="left" style="width: 210px; height: 1px;"><div style="margin: 10px;">本邮件无须回复，谢谢。</div></div>';
        return Tool::sendMail($email,$title.$subject,$body);
    }
}