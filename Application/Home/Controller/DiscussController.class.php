<?php
namespace Home\Controller;
use Home\Common\HomeController;
use \Think\Verify;
use Org\Tool\Tool;
class DiscussController extends HomeController {
    private $verification_id = 'discussCode';
    private $verify;
    public function _initialize() {
        $this->verify = new Verify();
        $this->sessionStart();
    }
    /**
     * 写入评论
     */
    public function addDiscuss() {
        $rules = array(
            array('nickname','require','请填写昵称',1),
            array('email','require','请填写邮箱',1),
            array('email',"isEmail",'请填写正确格式的邮箱',1,'callback'),
            array('content','require','请填写评论',1),
            array('captcha','require','请填写验证码',1),
            array('doc_id',"gtZeroInt",'文档ID错误',1,'callback'),
            array('pid',"egtZeroInt",'引用ID错误',1,'callback'),
        );

        $DocDiscuss = D('DocDiscuss');
        $data = $DocDiscuss->validate($rules)->create();
        if(!$data) $this->error($DocDiscuss->getError());

        $email = I('post.email','');
        $nickname = I('post.nickname','');

        if(!$this->verify->check(I('post.captcha',''),$this->verification_id)) {
            $this->error('验证码错误','',2);
        }

        $doc = D('Doc')->where(array('doc_id'=>$data['doc_id'],'state'=>2))->find();
        if(empty($doc)) $this->error('文档ID错误2','',2);
        $title = $doc['title'];

        if(intval($data['pid']) > 0) {
            $oldDiscuss = D('DocDiscuss')->where(array('discuss_id'=>$data['pid']))->find();
            if(empty($oldDiscuss)) $this->error('引用ID错误2','',2);
            $parentContent = $oldDiscuss['content'];
            $sendEmail = D('Ucenter')->where(array('uid'=>$oldDiscuss['uid']))->getField('email');
        }else{
            $parentContent = '';
            $sendEmail = D('Ucenter')->where(array('uid'=>$doc['create_id']))->getField('email');
        }

        $uinfo = D('Ucenter')->getUinfoByEmail($email);
        if(empty($uinfo)) {
            $uid = D('Ucenter')->addUser($email,'',3,$nickname);
            if(empty($uid)) {
                $this->error('抱歉，程序运行错误','',2);
            }
        }else{
            $utype = session(C('USER_TYPE_KEY'));
            if($uinfo['utype'] == 2 && (empty($utype) || $utype !=2 )) {
                $this->error('邮箱不对','',2);
            }elseif($uinfo['nickname'] != $nickname){
                $this->error('昵称不对','',2);
            }
            $uid = $uinfo['uid'];
        }

        $data['uid'] = $uid;
        $data['createtime'] = date('Y-m-d H:i:s');
        $result = D('DocDiscuss')->data($data)->add();

        if(empty($result)) $this->error('抱歉，回复失败','',2);

        $url = U('Home/Desc/index',array('doc_id'=>$data['doc_id']));
        $result = $this->sendMail($sendEmail,$title,$url,$data['content'],$parentContent);

        $this->success('','恭喜，回复成功');
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
    /**
     * 根据邮箱返回昵称
     */
    public function getNicknameByEmail() {
        $email = I('post.email','');
        if(empty($email)) $this->error();
        $uinfo = D('Ucenter')->getUinfoByEmail($email);
        if(empty($uinfo)) $this->error();
        $this->success($uinfo['nickname']);
    }
    /**
     * 验证码生成
     */
    public function verify(){
        $this->verify->length = 4;
        $this->verify->imageW = 200;
        $this->verify->imageH = 32;
        $this->verify->fontSize = 15;
        $this->verify->useCurve = false;
        $this->verify->entry($this->verification_id);
    }
}