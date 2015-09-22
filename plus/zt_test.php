<?php
include_once '../weixin/weixin.auth-0828.php';	

/*
$url='http://m.zuikuh5.com/a/229199_662784.html';

$rs=fetch_urlpage_contents($url);
$rs=get_replace($rs);
print_r($rs);


*/


$u='http://m.zuikuapp.com/a/98679_591262.html';






//截取内容
function get_str_content($start,$end,$rs){
   $p='/'.$start.'(.*)'.$end.'/'; 
   preg_match($p,$rs,$return_arr); 
   return $return_arr[1];
}


//转换成时间戳
function str2time($str){
    
    $year=date('Y',time());//取得年份
    $month=((int)substr($str,0,2));//取得月份   
    $day=((int)substr($str,4,2));//取得几号    
    $hour=((int)substr($str,10,2));//取得小时
    $min=((int)substr($str,13,2));//取得分钟
    //echo $month.'|'.$day.'|'.$hour.'|'.$min.'|';
    $time=mktime($hour,$min,0,$month,$day,$year);
    //echo $time;
    return $time;
}





//获取目标文件
function fetch_urlpage_contents($url){
    $c=file_get_contents($url);
    return $c;
}

//获取匹配内容
function fetch_match_contents($begin,$end,$c)
{
    $begin=empty($begin)?'':change_match_string($begin);
    $end=empty($end)?'':change_match_string($end);

    $p = "#{$begin}(.*){$end}#iU";//i表示忽略大小写，U禁止贪婪匹配
    
    
    if(preg_match_all($p,$c,$rs))
    {
        return $rs;
    }
    else { return "";}
}//转义正则表达式字符串

function change_match_string($str){
//注意，以下只是简单转�?
    $old=array("/","$",'?');
    $new=array("/","$",'?');
    $str=str_replace($old,$new,$str);
    return $str;
}

//采集网页
function pick($url,$ft)
{
    $c=fetch_urlpage_contents($url);
    foreach($ft as $key => $value)
    {
        $rs[$key]=fetch_match_contents($value["begin"],$value["end"],$c);
    }
return $rs;
}

//替换字符�?
function get_replace($rs){

    $rs=str_replace('<img src="/res/','<img src="http://m.zuikuh5.com/res/',$rs);
    $rs=str_replace('/file2/','http://m.zuikuh5.com/file2/',$rs);
	$rs=str_replace('/src="about:blank"','',$rs);
    //$rs=str_replace('seajs.use(["logic/default/','seajs.use(["http://m.zuikuh5.com/logic/default/',$rs);

    return $rs;
}
?>


<style type="text/css">
    body{ margin: 0rem; padding: 0;border: 0;}
    #iframe{ width: 100%; height: 100%;}
    /*.mask{ width: 100%; height: 100%;position: fixed; z-index: 99999; background: #000; opacity: 0.5; top: 0rem; left: 0rem;}*/
</style>
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<div id="iframe"><iframe src="<?php echo $u?>" frameborder="0" id="iframe" name="iframe" style="display:block"></iframe></div>

<script type="text/javascript" src="http://m.meilala.net/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>















