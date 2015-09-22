<?php
	require_once '../inc/common.php';
	$user = getUser('mid');
	$time = TIME;
	$cdTime = 0;
	$cdTime2 = 0;
	$status = 0; //未开始
	$aid = isset($_GET['aid']) ? (int) $_GET['aid'] : 0;
	if(!$aid)
	{
		header("Location:activity.php");
		exit;	
	} 
	
	$sql = "SELECT aid,name,aimg,stime,etime,quota,enroll,contens,ispay FROM m_activites WHERE aid = :aid";
	$sth = $db->prepare($sql);
	$sth->bindParam(':aid', $aid);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	
	//是否已报名
	$sql = "SELECT eid ,pay_stat FROM m_atv_enroll WHERE aid = '$aid' AND mid='$user[mid]' LIMIT 1";
	$sth = $db->prepare($sql);
	$sth->execute();
	$isApply = $sth->fetch(PDO::FETCH_ASSOC);

	
	$is_best = 0; //是否已赞
	$is_collect = 0; //是否已收藏
	$sql = "SELECT acttype FROM m_house_zan WHERE pid = '$aid' AND type = 1 AND mid='$user[mid]' ";
	$sth = $db->prepare($sql);
	$sth->execute();
	while($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		if($row['acttype'] == 1) $is_best = 1;
		if($row['acttype'] == 0) $is_collect = 1;
	}
	
	if($data['stime'] > $time)
	{
		$cdTime  = $data['stime']- $time;
		$cdTime2 = ($data['etime']- $data['stime']) - ($time-$data['stime']);
			
	}
	elseif($data['stime'] < $time && $time < $data['etime'] && $data['enroll'] < $data['quota'])
	{
		$status = 1;//活动进行中	
		$cdTime = ($data['etime']- $data['stime']) - ($time-$data['stime']);
	}	
	elseif($data['etime'] < $time || $data['enroll'] >= $data['quota'])
	{
		$status = 2;//活动结束	
	}
		
	
	//赞，收藏，评价
	$sql = "SELECT count(1) as count FROM m_house_zan WHERE `pid` = :aid AND `type` = '1' AND `acttype` = '0'";
	$sth = $db->prepare($sql);
	$sth->bindParam(':aid', $aid);
	$sth->execute();
	$house = $sth->fetchColumn();	

	$sql = "SELECT count(1) as count FROM m_house_zan WHERE pid = :aid AND type = '1' AND acttype = '1'";
	$sth = $db->prepare($sql);
	$sth->bindParam(':aid', $aid);
	$sth->execute();
	$zan = $sth->fetchColumn();
	
	$sql = "SELECT count(1) as count FROM m_comment WHERE pid = :aid AND type = '1'";
	$sth = $db->prepare($sql);
	$sth->bindParam(':aid', $aid);
	$sth->execute();
	$comment = $sth->fetchColumn();

	//获取订单号
	$sql = "SELECT order_no  FROM m_order WHERE pid =  $aid AND type = '1' AND mid='$user[mid]'  ORDER BY id desc";
	$sth = $db->prepare($sql);
	$sth->execute();
	$my_order = $sth->fetch();
	//print_r($order_no) ;exit;

	$ckData = $data;
	unset($ckData['contens']);
	$cookieApi = new HistoryApi();
	$cookieApi->history_sign = 'a_' . $data['aid'];
	$cookieApi->add_history($ckData);




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $data['name'];?></title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/activites/activity.php"></a></div>
    <h2>活动详情</h2>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<div class="act_dtl_top  mb10">
        <button class="button1 on">活动介绍</button>
        <button class="button2 " onclick="window.location.href='comment_list.php?aid=<?php echo $data['aid'];?>'">活动评价</button>
    </div>
    
    <div class="act_dtl_ct" id="atv_<?php echo $aid;?>">
    	<div class="act_img"><img src="<?php echo IMG_PATH . $data['aimg'];?>" /></div>
        
        <div class="ct">
        	<div class="title mt10"><?php echo $data['name'];?></div>
            <div class="time mt10 mb10">
            	<span>
                	<?php if($status == 0):?>
                	活动开始倒计时：
                    <?php elseif($status == 1):?>
                    离活动结束还有：
                    <?php endif;?>
                </span>
                
                <b>23</b>天<b>23</b>时<b>23</b>分<b>23</b>秒
            </div>
            <div class="apply mb10">报名人数<b><?php echo number_format($data['enroll']);?></b>名，只剩名额<b><?php echo number_format($data['quota'] - $data['enroll']);?></b>名</div>
            <?php if($status == 0):?>
            <div class="consult"><button>活动未开始</button></div>
            <?php elseif($status == 2):?>
            <div class="consult"><button>活动已结束</button></div>
            <?php elseif($status == 1):?>
            	<div class="consult consult_ing">
                
                	<?php if($isApply):?>
                		<?php if($isApply['pay_stat']==0):?>
                			<button onclick="window.location.href='../plus/go_pay.php?order_no=<?php echo $my_order['order_no'];?>'">待支付</button>
                		<?php else: ?>
                    		<button>您已报名</button>
                    	<?php endif;?>	
                    <?php else:?>
                    	<button onclick="window.location.href='activity_apply.php?aid=<?php echo $data['aid'];?>'">
                        立即报名
                        </button>
                    <?php endif;?>
               	
                </div>
            <?php endif;?>
            
            <div class="operate">
            	<a href="comment_list.php?aid=<?php echo $aid?>" class="comment">评论<b><?php echo number_format($comment);?></b></a>
                <a href="javascript:my_operate(0,1,<?php echo $is_collect;?>,<?php echo $aid?>);" class="collect" id="0-1-<?php echo $aid;?>">收藏<b><?php echo number_format($house);?></b></a>
                <a href="javascript:my_operate(1,1,<?php echo $is_best;?>,<?php echo $aid?>);" class="best" id="1-1-<?php echo $aid;?>">赞<b><?php echo number_format($zan);?></b></a>
            </div>
        </div>
        
    </div>
    <div class="act_dtl_ct border white pb10 mt10 mb10">
    	<?php $contens = explode('{|}', $data['contens']);?>
		<?php foreach($contens as $v):;?>
			<?php $arr = explode('{-}', $v);?>
            <?php if($arr[0]):?>
                <div class="title main_color pt10 pb10"><?php echo $arr[0];?></div>
            <?php endif;?>
            <div class="act_ct limit_img"><?php echo $arr[1];?></div>
        <?php endforeach;?>
    </div>
    
</div>

<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
/*时间倒计时
* time 倒计时秒数
* id 倒计时容器id
* day_elem 天数calss
* hour_elem 小时calss
* minute_elem 分钟calss
* second_elem 秒数calss
*/
function countDown(time,cdTime2,id,status){
	//var end_time = new Date(time).getTime(),//月份是实际月份-1
	//sys_second = (end_time-new Date().getTime())/1000;
	var timer = setInterval(function(){
		var idDom = $('#atv_'+id);
		if (time > 0) {
			time -= 1;
			var day = Math.floor((time / 3600) / 24);
			var hour = Math.floor((time / 3600) % 24);
			var minute = Math.floor((time / 60) % 60);
			var second = Math.floor(time % 60);
			
			idDom.find('b').eq(0).text(day);//计算天
			idDom.find('b').eq(1).text(hour<10?"0"+hour:hour);//计算小时
			idDom.find('b').eq(2).text(minute<10?"0"+minute:minute);//计算分
			idDom.find('b').eq(3).text(second<10?"0"+second:second);// 计算秒
		} else { 
			
			if(status == 0) 
			{
				clearInterval(timer);
				idDom.find('.consult').html('<button onclick=window.location.href="activity_apply.php?aid='+id+'">立即报名</button>');
				idDom.find('.time span').html('活动结束倒计时：');
				idDom.find('.consult').addClass('consult_ing');
				countDown(cdTime2,0,id,1);
				idDom.find('.time').show();
			}	
			else if(status == 1)
			{
				idDom.find('.time').hide();
				idDom.find('.consult').removeClass('consult_ing');
				idDom.find('.consult').html('<button>活动已结束</button>');
				clearInterval(timer);
			}
			else
			{
				idDom.find('.time').hide();
				clearInterval(timer);	
			}
				
		}
	}, 1000);
}
countDown(<?php echo $cdTime;?>,<?php echo $cdTime2;?>,<?php echo $aid;?>,<?php echo $status;?>);


window.onscroll = function(){ 
	var h=$(".header").height();
	var h1=$(".act_dtl_top").height();
    var t = document.documentElement.scrollTop || document.body.scrollTop;  
	if(t<=h){
		$('.act_dtl_top').css({"position":"relative"});
		$('.act_dtl_top button').css({"margin-top": "0.2rem"});
	}
    else { 
		$('.act_dtl_top').css({"position":"fixed","width":"100%","top":"0px","max-width":"640px","margin-top":"0px","margin-left":"auto","margin-right":"auto"});
		$('.act_dtl_top button').css({"margin-top": "0rem"});
    }
} 
</script>
</body>
</html>
