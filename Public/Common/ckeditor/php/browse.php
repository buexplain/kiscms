<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="EditPlus®">
<meta name="Author" content="Buexplain">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>服务器资源浏览</title>
<script type="text/javascript" src="/jquery.js"></script>
<style>
    /*公共样式public*/
    body,p{
        margin:0; padding:0; border:0;
        color: #333;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 14px;
        line-height: 200%;
        width: 100%;
        height: 100%;
    }
    ul,ol{list-style:none;margin:0; padding:0;}

    a{text-decoration:none;color: #337ab7;}
    a:hover{color: blue;color: #23527c;text-decoration:underline;}
    a:visited {color: red;}

    .text-center{
        text-align: center;
    }
    .container{
        width: 1000px;
        margin: 0 auto;
    }
    .header{
        margin: 20px 0;

    }
    .nav,.subdir{
        color: #337ab7;
        border-bottom: 1px solid #337ab7;
    }
    .subdir a{
        margin-right: 20px;
    }

    table.gridtable {
        font-family:verdana,arial,sans-serif;
        font-size:11px;
        color:#333333;
        border-width:1px;
        border-color:#666666;
        border-collapse:collapse;
        width: 100%;
    }
    table.gridtable th {
        border-width:1px;
        padding:8px;
        border-style:solid;
        border-color:#666666;
        background-color:#dedede;
    }
    table.gridtable
        td {
        border-width:1px;
        padding:8px;
        border-style:solid;
        border-color:#666666;
        background-color:#ffffff;
    }
    .showImg,.loupe{
        cursor: pointer;
    }
</style>
<script>
    var callback = <?php echo isset($_GET['CKEditorFuncNum']) ? intval($_GET['CKEditorFuncNum']) : 0;?>;
    function returnFile(fileName,msg) {
        if(fileName) {
            window.top.opener.CKEDITOR.tools.callFunction(callback,fileName,'');
            window.close();
        }else{
            window.top.opener.CKEDITOR.tools.callFunction(callback,'',msg);
        }
    }
</script>
</head>
<?php
    session_start();
    if(!isset($_SESSION['nav'])) $_SESSION['nav'] = [];

    $rootDir = '../staticData';
    $rootUrl = '/staticData';
    $showImg = '/showimg.png';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : '';
    $step = isset($_GET['step']) ? $_GET['step'] : 0;
    $relDir = getRelDir($step);

    $curDir = $rootDir.'/'.$relDir.'/'.$dir;

    $dirArr = getDir($curDir);


    $_SESSION['nav'][$step] = array('txt'=>($step == 0 ? '根目录' : $dir),'href'=>$_SERVER['REQUEST_URI']);
    $fileArr = getFile($curDir);
    function getRelDir($step) {
        $result = '';
        for($i=0;$i<$step;$i++) {
            if(!isset($_SESSION['nav'][$i]['href'])) continue;
            $tmp = $_SESSION['nav'][$i]['href'];
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
    /**
     * 字节转换
     */
    function byte($num) {
        $bitunit = array(' B', ' KB', ' MB', ' GB');
        for($key = 0;$key < count($bitunit);$key++) {
            if ($num >= pow(2, 10 * $key)-1) { // 1023B 会显示为 1KB
                $num_bitunit_str = (ceil($num / pow(2, 10 * $key) * 100) / 100) . " $bitunit[$key]";
            }
        }
        return $num_bitunit_str;
    }
    /**
     * 获取目录
     */
    function getDir($dir) {
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
     * 获取文件
     */
    function getFile($dir) {
        $dir = realpath($dir);
        if(!is_dir($dir)) return false;
        $handle = opendir($dir);
        if(!$handle) return false;
        $fileArr = array();
        while ($tmp = readdir($handle)) {
            $tmp2 = $dir.'/'.$tmp;
            if(is_file($tmp2)) {
                $fileArr[] = ['name'=>$tmp,'size'=>filesize($tmp2),'time'=>filemtime($tmp2)];
            }
        }
        closedir($handle);
        return $fileArr;
    }
    /**
     * 生成url
     */
    function url($url,$params=array()) {
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

?>
<body>
    <div class="container">
        <div class="header">
            <p class="nav">
                <?php
                    $tmp = "";
                    $tmp2 = '&nbsp;>&nbsp;';
                    foreach ($_SESSION['nav'] as $key => $value) {
                        if($key <= $step) {
                            $tmp .= '<a href="'.$value['href'].'">'.$value['txt'].'</a>'.$tmp2;
                        }else{
                            unset($_SESSION['nav'][$key]);
                        }
                    }
                    echo substr($tmp,0,-strlen($tmp2));
                ?>
            </p>
            <?php if($dirArr) {?>
            <p class="subdir">
                <?php
                    foreach ($dirArr as $key => $value) {
                        $url = url($_SERVER['REQUEST_URI'],['dir'=>$value,'step'=>1+$step]);
                ?>
                <a href="<?php echo $url;?>"><?php echo $value;?></a>
                <?php } ?>
            </p>
            <?php } ?>
        </div>
        <div class="list">
            <table class="gridtable">
                <tr>
                    <th>
                        名称
                    </th>
                    <th>
                        大小
                    </th>
                    <th>
                        时间
                    </th>
                    <th>
                        <img src="<?php echo $showImg;?>" title="点击显示图片" class="loupe">
                    </th>
                    <th>
                        操作
                    </th>
                </tr>
                <?php
                    foreach ($fileArr as $key => $value) {
                        $url = $rootUrl.'/'.$relDir.'/'.$dir.'/'.$value['name'];
                        $value['isImg'] = in_array(strtolower(pathinfo($value['name'],PATHINFO_EXTENSION)),['jpg','jpeg','gif','png','bmp']);
                ?>
                <tr>
                    <td>
                        <?php echo $value['name'];?>
                    </td>
                     <td>
                        <?php echo byte($value['size']);?>
                    </td>
                    <td>
                        <?php echo date('Y-m-d H:i:s',$value['time']);?>
                    </td>
                    <td class="text-center">
                        <?php
                            if($value['isImg']) {
                        ?>
                            <img src="<?php echo $showImg;?>" class="showImg" data-url="<?php echo $url;?>">
                        <?php } ?>
                    </td>
                    <td class="text-center">
                        <a href="javascript:;" onclick="returnFile('<?php echo $url;?>')">确定</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <script>
        $('.showImg').on('click',function(i){
            var o = $(this);
            var dataUrl = o.attr('data-url');
            var img = o.attr('src');
            o.attr('src',dataUrl);
            o.attr('data-url',img);
            o.css({"max-width":"700px"});
        });
        $('.loupe').on('click',function(){
            $('.showImg').click();
        });

    </script>
</body>
</html>