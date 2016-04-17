<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
class FileManagerController extends BaseController {
    public function index() {
        $nav = session('nav');
        if(empty($nav)) {
            $nav = [];
            session('nav',$nav);
        }
        $callBackId = I('get.CKEditorFuncNum',0,'intval');
        $rootDir = ENTRY."StaticData/";
        $rootUrl = C('site.staticUrl').'StaticData';
        $showImg = C('site.staticUrl').'Public/Common/showimg.png';
        $dir = I('get.dir','');
        $step = I('get.step',0,'intval');
        $relDir = $this->getRelDir($step,$nav);
        $curDir = $rootDir.'/'.$relDir.'/'.$dir;
        $dirArr = $this->getDir($curDir);
        $tmp = [];
        foreach ($dirArr as $key => $value) {
            $tmp[$key]['name'] = $value;
            $tmp[$key]['url'] = $this->url($_SERVER['REQUEST_URI'],['dir'=>$value,'step'=>1+$step]);
        }
        $dirArr = $tmp;

        $nav[$step] = array(
            'txt'=>($step == 0 ? '根目录' : $dir),
            'href'=>$_SERVER['REQUEST_URI']
        );
        $fileArr = [];
        if($step > 1) $fileArr = $this->getFile($curDir);
        foreach ($fileArr as $key => $value) {
            $fileArr[$key]['size'] = $this->byte($value['size']);
            $fileArr[$key]['url'] = $rootUrl.'/'.$relDir.'/'.$dir.'/'.$value['name'];
            $fileArr[$key]['isImg'] = in_array(strtolower(pathinfo($value['name'],PATHINFO_EXTENSION)),['jpg','jpeg','gif','png','bmp']);
        }

        $this->assign('nav',$nav);
        $this->assign('step',$step);
        $this->assign('dirArr',$dirArr);
        $this->assign('fileArr',$fileArr);
        $this->assign('showImg',$showImg);
        $this->assign('callBackId',$callBackId);
        $this->display();
    }
    /**
     * 生成url
     */
    protected function url($url,$params=array()) {
        $a=parse_url($url);
        $a['query']=isset($a['query'])?$a['query']:'';
        $b=explode('&',$a['query']);//得到参数
        $c=array();
        foreach($b as $k=>$v){//拆解重组参数
            $tmp=explode('=',$v);
            if(count($tmp)>1){
                $c[$tmp[0]]=$tmp[1];
            }else{
                if($tmp[0]!='') $c[$tmp[0]]='';
            }
        }

        foreach($params as $k=>$v){//替换参数
            $c[$k]=$v;
        }
        //回拼字符串
        $d=array();
        foreach($c as $k=>$v){
            array_push($d,$k.'='.$v);
        }
        $query=implode('&',$d);
        $url=(isset($a['path'])?$a['path']:'').'?'.$query; //$_SERVER['SCRIPT_NAME']
        return $url;
    }
    /**
     * 字节转换
     */
    protected function byte($num) {
        $bitunit = array(' B', ' KB', ' MB', ' GB');
        for($key = 0;$key < count($bitunit);$key++) {
            if ($num >= pow(2, 10 * $key)-1) { // 1023B 会显示为 1KB
                $num_bitunit_str = (ceil($num / pow(2, 10 * $key) * 100) / 100) . " $bitunit[$key]";
            }
        }
        return $num_bitunit_str;
    }
    /**
     * 获取文件
     */
    protected function getFile($dir) {
        $dir = realpath($dir);
        if(!is_dir($dir)) return false;
        $handle = opendir($dir);
        if(!$handle) return false;
        $fileArr = array();
        while ($tmp = readdir($handle)) {
            $tmp2 = $dir.'/'.$tmp;
            if(is_file($tmp2)) {
                $fileArr[] = ['name'=>$tmp,'size'=>filesize($tmp2)];
            }
        }
        closedir($handle);
        return $fileArr;
    }
     /**
     * 获取目录
     */
    protected function getDir($dir) {
        $dir = realpath($dir);
        if(!is_dir($dir)) return false;
        $handle = opendir($dir);
        if(!$handle) return false;
        $dirArr = array();
        while ($tmp = readdir($handle)) {
            $tmp2 = $dir.'/'.$tmp;
            if(is_dir($tmp2) && !in_array($tmp,array('.','..'))) {
                $dirArr[] = $tmp;
            }
        }
        closedir($handle);
        return $dirArr;
    }
    /**
     * 获取相对路径
     */
    protected function getRelDir($step,$nav) {
        $result = '';
        for($i=0;$i<$step;$i++) {
            if(!isset($nav[$i]['href'])) continue;
            $tmp = $nav[$i]['href'];
            $a=parse_url($tmp);
            $a['query']=isset($a['query'])?$a['query']:'';
            $b=explode('&',$a['query']);//得到参数
            $c=array();
            foreach($b as $k=>$v){//拆解重组参数
                $tmp=explode('=',$v);
                if(count($tmp)>1){
                    $c[$tmp[0]]=$tmp[1];
                }else{
                    if($tmp[0]!='') $c[$tmp[0]]='';
                }
            }
            if(isset($c['dir'])) $result .= '/'.$c['dir'];
        }
        $result = trim($result,'/');
        return $result;
    }
}