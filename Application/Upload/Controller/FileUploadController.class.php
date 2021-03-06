<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
use \Think\Upload;
class FileUploadController extends BaseController {
    /**
     * 单文件上传
     */
    public function index(){
        $result = $this->verifyToken(I('post.token',''));
        if($result === false) {
            $this->error('上传令牌错误，请刷新上传框！');
        }

        $fileVal = I('post.fileVal','file');
        $fileArr = isset($_FILES[$fileVal])?$_FILES[$fileVal]:array();
        if(empty($fileArr)) $this->error('$_FILES key 不存在');
        $folder = $this->getFolder(strtolower(pathinfo($fileArr['name'],PATHINFO_EXTENSION)));
        if($folder === false) $this->error('mimetypes错误!');

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
            'url'=>C('fileStaticUrl').$dir,
            'oname'=>trim(substr($result['name'],0,0-strlen($result['ext'])-1)),
            'ext'=>$result['ext'],
        );

        //如果当前上传模块部署到了静态服务器上，那么session没有做共享，则无法获取uid
        $uid = $this->getUid();

        $fileArr = array(
            'md5'=>$result['md5'],
            'uid'=>$uid,
            'oname'=>$return['oname'],
            'dir'=>$dir,
            'ext'=>$result['ext'],
            'size'=>$result['size'],
            'createtime'=>date('Y-m-d H:i:s')
        );

        $result = D('File')->data($fileArr)->add();
        if(empty($result)) {
            unlink($dir);
            $this->error('写入数据库失败！');
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
        $tokenNum = 0; //表单令牌可用次数
        foreach ($md5Arr as $key => $value) {
            if(isset($result[$value])) {
                $md5Arr[$key] = array();
                $md5Arr[$key]['md5'] = $value;
                $md5Arr[$key]['data'] = $result[$value];
                $md5Arr[$key]['data']['url'] = C('fileStaticUrl').$md5Arr[$key]['data']['url'];
                unset($md5Arr[$key]['data']['md5']);
            }else{
                $tokenNum++;
                unset($md5Arr[$key]);
            }
        }

        if($tokenNum > 0) {
            $this->setTokenNum($tokenNum,$this->getToken());
        }else{
            $this->destroyToken();
        }

        $this->success($md5Arr);
    }
    protected function success($data='',$msg='success') {
        $this->ajaxReturn($this->ajaxData(0,$msg,$data));
    }
    protected function error($msg) {
        $this->ajaxReturn($this->ajaxData(1,$msg,''));
    }
    /**
     * 允许上传的文件后缀
     */
    protected function getExt() {
        $fileUploadExt = C('fileUploadExt');
        if(empty($fileUploadExt)) {
            return array();
        }else{
            $fileUploadExt = explode(',',$fileUploadExt);
        }
        return $fileUploadExt;
    }
    /**
     * 根据文件后缀获取分类文件夹名
     */
    protected function getFolder($ext) {
        //mimetypes
        $extArr = array(
            'ez' => 'application',
            'anx' => 'application',
            'atom' => 'application',
            'atomcat' => 'application',
            'atomsrv' => 'application',
            'lin' => 'application',
            'cap' => 'application',
            'pcap' => 'application',
            'cu' => 'application',
            'davmount' => 'application',
            'tsp' => 'application',
            'es' => 'application',
            'spl' => 'application',
            'hta' => 'application',
            'jar' => 'application',
            'ser' => 'application',
            'class' => 'application',
            'js' => 'application',
            'm3g' => 'application',
            'hqx' => 'application',
            'nb' => 'application',
            'nbp' => 'application',
            'mdb' => 'application',
            'doc' => 'application',
            'dot' => 'application',
            'mxf' => 'application',
            'bin' => 'application',
            'oda' => 'application',
            'ogx' => 'application',
            'pdf' => 'application',
            'key' => 'application',
            'pgp' => 'application',
            'prf' => 'application',
            'ps' => 'application',
            'ai' => 'application',
            'eps' => 'application',
            'epsi' => 'application',
            'epsf' => 'application',
            'eps2' => 'application',
            'eps3' => 'application',
            'rar' => 'application',
            'rdf' => 'application',
            'rss' => 'application',
            'rtf' => 'application',
            'smil' => 'application',
            'xhtml' => 'application',
            'xht' => 'application',
            'xml' => 'application',
            'xsl' => 'application',
            'xsd' => 'application',
            'xspf' => 'application',
            'zip' => 'application',
            'apk' => 'application',
            'cdy' => 'application',
            'kml' => 'application',
            'kmz' => 'application',
            'xul' => 'application',
            'xls' => 'application',
            'xlb' => 'application',
            'xlt' => 'application',
            'cat' => 'application',
            'stl' => 'application',
            'ppt' => 'application',
            'pps' => 'application',
            'xlsx' => 'application',
            'xltx' => 'application',
            'pptx' => 'application',
            'ppsx' => 'application',
            'potx' => 'application',
            'docx' => 'application',
            'dotx' => 'application',
            'cod' => 'application',
            'mmf' => 'application',
            'sis' => 'application',
            'vsd' => 'application',
            'wbxml' => 'application',
            'wmlc' => 'application',
            'wmlsc' => 'application',
            'wpd' => 'application',
            'wp5' => 'application',
            'wk' => 'application',
            '7z' => 'application',
            'abw' => 'application',
            'dmg' => 'application',
            'bcpio' => 'application',
            'torrent' => 'application',
            'cab' => 'application',
            'cbr' => 'application',
            'cbz' => 'application',
            'cdf' => 'application',
            'cda' => 'application',
            'vcd' => 'application',
            'pgn' => 'application',
            'cpio' => 'application',
            'deb' => 'application',
            'udeb' => 'application',
            'dcr' => 'application',
            'dir' => 'application',
            'dxr' => 'application',
            'dms' => 'application',
            'wad' => 'application',
            'dvi' => 'application',
            'rhtml' => 'application',
            'pfa' => 'application',
            'pfb' => 'application',
            'gsf' => 'application',
            'pcf' => 'application',
            'pcf.Z' => 'application',
            'mm' => 'application',
            'gnumeric' => 'application',
            'sgf' => 'application',
            'gcf' => 'application',
            'gtar' => 'application',
            'tgz' => 'application',
            'taz' => 'application',
            'hdf' => 'application',
            'phtml' => 'application',
            'pht' => 'application',
            'php' => 'application',
            'phps' => 'application',
            'php3' => 'application',
            'php3p' => 'application',
            'php4' => 'application',
            'php5' => 'application',
            'ica' => 'application',
            'info' => 'application',
            'ins' => 'application',
            'isp' => 'application',
            'iii' => 'application',
            'iso' => 'application',
            'jam' => 'application',
            'jnlp' => 'application',
            'jmz' => 'application',
            'chrt' => 'application',
            'kil' => 'application',
            'skp' => 'application',
            'skd' => 'application',
            'skt' => 'application',
            'skm' => 'application',
            'kpr' => 'application',
            'kpt' => 'application',
            'ksp' => 'application',
            'kwd' => 'application',
            'kwt' => 'application',
            'latex' => 'application',
            'lha' => 'application',
            'lyx' => 'application',
            'lzh' => 'application',
            'lzx' => 'application',
            'frm' => 'application',
            'maker' => 'application',
            'frame' => 'application',
            'fm' => 'application',
            'fb' => 'application',
            'book' => 'application',
            'fbdoc' => 'application',
            'wmd' => 'application',
            'wmz' => 'application',
            'com' => 'application',
            'exe' => 'application',
            'bat' => 'application',
            'dll' => 'application',
            'msi' => 'application',
            'nc' => 'application',
            'pac' => 'application',
            'dat' => 'application',
            'nwc' => 'application',
            'o' => 'application',
            'oza' => 'application',
            'p7r' => 'application',
            'crl' => 'application',
            'pyc' => 'application',
            'pyo' => 'application',
            'qgs' => 'application',
            'shp' => 'application',
            'shx' => 'application',
            'qtl' => 'application',
            'rpm' => 'application',
            'rb' => 'application',
            'shar' => 'application',
            'swf' => 'application',
            'swfl' => 'application',
            'scr' => 'application',
            'sit' => 'application',
            'sitx' => 'application',
            'sv4cpio' => 'application',
            'sv4crc' => 'application',
            'tar' => 'application',
            'gf' => 'application',
            'pk' => 'application',
            'texinfo' => 'application',
            'texi' => 'application',
            '~' => 'application',
            '%' => 'application',
            'bak' => 'application',
            'old' => 'application',
            'sik' => 'application',
            't' => 'application',
            'tr' => 'application',
            'roff' => 'application',
            'man' => 'application',
            'me' => 'application',
            'ms' => 'application',
            'ustar' => 'application',
            'src' => 'application',
            'wz' => 'application',
            'crt' => 'application',
            'xcf' => 'application',
            'fig' => 'application',
            'xpi' => 'application',
            'cpt' => 'image',
            'gif' => 'image',
            'ief' => 'image',
            'jpeg' => 'image',
            'jpg' => 'image',
            'jpe' => 'image',
            'pcx' => 'image',
            'png' => 'image',
            'svg' => 'image',
            'svgz' => 'image',
            'tiff' => 'image',
            'tif' => 'image',
            'djvu' => 'image',
            'djv' => 'image',
            'wbmp' => 'image',
            'cr2' => 'image',
            'crw' => 'image',
            'ras' => 'image',
            'cdr' => 'image',
            'pat' => 'image',
            'cdt' => 'image',
            'erf' => 'image',
            'ico' => 'image',
            'art' => 'image',
            'jng' => 'image',
            'bmp' => 'image',
            'nef' => 'image',
            'orf' => 'image',
            'psd' => 'image',
            'pnm' => 'image',
            'pbm' => 'image',
            'pgm' => 'image',
            'ppm' => 'image',
            'rgb' => 'image',
            'xbm' => 'image',
            'xpm' => 'image',
            'xwd' => 'image',
            'smi' => '#chemical',
            'mif' => '#chemical',
            'csh' => 'text',
            'sh' => 'text',
            'tcl' => 'text',
            'manifest' => 'text',
            'ics' => 'text',
            'icz' => 'text',
            'css' => 'text',
            'csv' => 'text',
            '323' => 'text',
            'html' => 'text',
            'htm' => 'text',
            'shtml' => 'text',
            'uls' => 'text',
            'mml' => 'text',
            'asc' => 'text',
            'txt' => 'text',
            'text' => 'text',
            'pot' => 'text',
            'brf' => 'text',
            'rtx' => 'text',
            'sct' => 'text',
            'wsc' => 'text',
            'tm' => 'text',
            'ts' => 'text',
            'tsv' => 'text',
            'jad' => 'text',
            'wml' => 'text',
            'wmls' => 'text',
            'bib' => 'text',
            'boo' => 'text',
            'h++' => 'text',
            'hpp' => 'text',
            'hxx' => 'text',
            'hh' => 'text',
            'c++' => 'text',
            'cpp' => 'text',
            'cxx' => 'text',
            'cc' => 'text',
            'h' => 'text',
            'htc' => 'text',
            'c' => 'text',
            'd' => 'text',
            'diff' => 'text',
            'patch' => 'text',
            'hs' => 'text',
            'java' => 'text',
            'lhs' => 'text',
            'moc' => 'text',
            'p' => 'text',
            'pas' => 'text',
            'gcd' => 'text',
            'pl' => 'text',
            'pm' => 'text',
            'py' => 'text',
            'scala' => 'text',
            'etx' => 'text',
            'tk' => 'text',
            'tex' => 'text',
            'ltx' => 'text',
            'sty' => 'text',
            'cls' => 'text',
            'vcs' => 'text',
            'vcf' => 'text',
            'amr' => 'audio',
            'awb' => 'audio',
            'axa' => 'audio',
            'au' => 'audio',
            'snd' => 'audio',
            'flac' => 'audio',
            'mid' => 'audio',
            'midi' => 'audio',
            'kar' => 'audio',
            'mpga' => 'audio',
            'mpega' => 'audio',
            'mp2' => 'audio',
            'mp3' => 'audio',
            'm4a' => 'audio',
            'm3u' => 'audio',
            'oga' => 'audio',
            'ogg' => 'audio',
            'spx' => 'audio',
            'sid' => 'audio',
            'aif' => 'audio',
            'aiff' => 'audio',
            'aifc' => 'audio',
            'gsm' => 'audio',
            'wma' => 'audio',
            'wax' => 'audio',
            'ra' => 'audio',
            'rm' => 'audio',
            'ram' => 'audio',
            'pls' => 'audio',
            'sd2' => 'audio',
            'wav' => 'audio',
            'alc' => 'chemical',
            'cac' => 'chemical',
            'cache' => 'chemical',
            'csf' => 'chemical',
            'cbin' => 'chemical',
            'cascii' => 'chemical',
            'ctab' => 'chemical',
            'cdx' => 'chemical',
            'cer' => 'chemical',
            'c3d' => 'chemical',
            'chm' => 'chemical',
            'cif' => 'chemical',
            'cmdf' => 'chemical',
            'cml' => 'chemical',
            'cpa' => 'chemical',
            'bsd' => 'chemical',
            'csml' => 'chemical',
            'csm' => 'chemical',
            'ctx' => 'chemical',
            'cxf' => 'chemical',
            'cef' => 'chemical',
            'emb' => 'chemical',
            'embl' => 'chemical',
            'spc' => 'chemical',
            'inp' => 'chemical',
            'gam' => 'chemical',
            'gamin' => 'chemical',
            'fch' => 'chemical',
            'fchk' => 'chemical',
            'cub' => 'chemical',
            'gau' => 'chemical',
            'gjc' => 'chemical',
            'gjf' => 'chemical',
            'gal' => 'chemical',
            'gcg' => 'chemical',
            'gen' => 'chemical',
            'hin' => 'chemical',
            'istr' => 'chemical',
            'ist' => 'chemical',
            'jdx' => 'chemical',
            'dx' => 'chemical',
            'kin' => 'chemical',
            'mcm' => 'chemical',
            'mmd' => 'chemical',
            'mmod' => 'chemical',
            'mol' => 'chemical',
            'rd' => 'chemical',
            'rxn' => 'chemical',
            'sd' => 'chemical',
            'sdf' => 'chemical',
            'tgf' => 'chemical',
            'mcif' => 'chemical',
            'mol2' => 'chemical',
            'b' => 'chemical',
            'gpt' => 'chemical',
            'mop' => 'chemical',
            'mopcrt' => 'chemical',
            'mpc' => 'chemical',
            'zmt' => 'chemical',
            'moo' => 'chemical',
            'mvb' => 'chemical',
            'asn' => 'chemical',
            'prt' => 'chemical',
            'ent' => 'chemical',
            'val' => 'chemical',
            'aso' => 'chemical',
            'pdb' => 'chemical',
            'ros' => 'chemical',
            'sw' => 'chemical',
            'vms' => 'chemical',
            'vmd' => 'chemical',
            'xtel' => 'chemical',
            'xyz' => 'chemical',
            'eml' => 'message',
            'igs' => 'model',
            'iges' => 'model',
            'msh' => 'model',
            'mesh' => 'model',
            'silo' => 'model',
            'x3dv' => 'model',
            'x3d' => 'model',
            'x3db' => 'model',
            'wrl' => 'x-world',
            'vrml' => 'x-world',
            'vrm' => 'x-world',
            '3gp' => 'video',
            'axv' => 'video',
            'dl' => 'video',
            'dif' => 'video',
            'dv' => 'video',
            'fli' => 'video',
            'gl' => 'video',
            'mpeg' => 'video',
            'mpg' => 'video',
            'mpe' => 'video',
            'mp4' => 'video',
            'qt' => 'video',
            'mov' => 'video',
            'ogv' => 'video',
            'mxu' => 'video',
            'flv' => 'video',
            'lsf' => 'video',
            'lsx' => 'video',
            'mng' => 'video',
            'asf' => 'video',
            'asx' => 'video',
            'wm' => 'video',
            'wmv' => 'video',
            'wmx' => 'video',
            'wvx' => 'video',
            'avi' => 'video',
            'movie' => 'video',
            'mpv' => 'video',
            'mkv' => 'video',
            'ice' => 'x-conference',
            'sisx' => 'x-epoc',
        );
        if(isset($extArr[$ext])) return $extArr[$ext];
        return false;
    }
}