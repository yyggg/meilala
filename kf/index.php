<?php

$url='http://www6.53kf.com/m.php?cid=72035610&arg=10035610&style=1';

$rs=fetch_urlpage_contents($url);
$rs=get_replace($rs);
print_r($rs);



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
//注意，以下只是简单转义
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

//替换字符串
function get_replace($rs){
    //$rs=str_replace('<a href="http://www.53kf.com/">53KF.com</a>','taom.com.cn',$rs);
    $rs=str_replace('</head>','<script src="public/style/mobile_new.js" type="text/javascript"></script><link rel="stylesheet" href="public/style/mobile_new.css" type="text/css" media="all"></head>',$rs);
    $rs=str_replace('<a class="logo" href="javascript:;">','<a href="/" class="header_back"><img src="../images/header_icon1b.png" />',$rs);
	$rs=str_replace('<h2 id="header-title">','<h2>在线专家</h2><h2 id="header-title">',$rs);
    $rs=str_replace('"mobile/','"http://www6.53kf.com/mobile/',$rs);
    $rs=str_replace('"min/','"http://www6.53kf.com/min/',$rs);
    $rs=str_replace('"img/','"http://www6.53kf.com/img/',$rs);
    $rs=str_replace('"js/','"http://www6.53kf.com/js/',$rs);
    $rs=str_replace('"include/','"http://www6.53kf.com/include/',$rs);
    $rs=str_replace('"impl/','"http://www6.53kf.com/impl/',$rs);
    //$rs=str_replace('class="comm-btn-focus btn-send"','class="comm-btn-focus btn-send mbtn_send" onclick="mbt_send();"',$rs);
    //$rs=str_replace('"img/face','"img/faces',$rs);
    return $rs;
}

?>
<script type="text/javascript">
	$("#header .logo").url('/');
</script>