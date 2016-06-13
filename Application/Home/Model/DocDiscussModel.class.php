<?php
namespace Home\Model;
use Common\Model\BaseModel;
class DocDiscussModel extends BaseModel{
    private $discussArr = array();
    public function getDiscussByDocID($doc_id,$page,$pagesize) {
        $result = $this->where(array('doc_id'=>$doc_id))->order('createtime desc')->page($page,$pagesize)->select();
        if(empty($result)) return array();
        foreach ($result as $key => $value) {
            $this->discussArr[$value['discuss_id']] = $value;
        }
        foreach ($result as $key => $value) {
            if($value['pid'] > 0) {
                $result[$key]['parentData'] = $this->getDiscussByDiscussID($value['pid']);
                $tmp = D('Uinfo')->getUinfoByUid($result[$key]['parentData']['uid']);
                $result[$key]['parentData']['nickname'] = isset($tmp['nickname']) ? $tmp['nickname'] : '';
                $result[$key]['parentData']['avatar']   = !empty($tmp['avatar'])   ? $tmp['avatar']   : $this->defaultAvatar();
                if($result[$key]['parentData']['state'] != 1) {
                    $result[$key]['parentData']['content'] = '哇咔咔，被和谐了 :)';
                }
            }else{
                $result[$key]['parentData'] = array();
            }
            $tmp = D('Uinfo')->getUinfoByUid($value['uid']);
            $result[$key]['nickname'] = isset($tmp['nickname']) ? $tmp['nickname'] : '';
            $result[$key]['avatar']   = !empty($tmp['avatar'])   ? $tmp['avatar']   : $this->defaultAvatar();
            if($result[$key]['state'] != 1) {
                $result[$key]['content'] = '哇咔咔，被和谐了 :)';
            }
        }
        return array('doc_id'=>$doc_id,'result'=>$result);
    }
    public function getDiscussByDiscussID($discussID) {
        if(!isset($this->discussArr[$discussID])) {
            $tmp = $this->where(array('discuss_id'=>$discuss_id))->find();
            $this->discussArr[$discussID] = array();
            if(!empty($tmp)) {
                $tmp2 = D('Uinfo')->getUinfoByUid($tmp['uid']);
                $tmp['nickname'] = isset($tmp2['nickname']) ? $tmp2['nickname'] : '';
                $tmp['avatar']   = !empty($tmp2['avatar'])   ? $tmp2['avatar']   : $this->defaultAvatar();
                $this->discussArr[$discussID] = $tmp;
            }
        }
        return $this->discussArr[$discussID];
    }
    private function defaultAvatar() {
        return '/Public/Home/image/default-avatar.gif';
    }
}