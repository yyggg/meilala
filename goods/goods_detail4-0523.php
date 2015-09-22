<?php
	require_once '../inc/common.php';
	$gid = isset($_GET['gid']) ? (int)$_GET['gid'] : 0;
	if(!$gid) header("location:goods.php");
	$sql = "SELECT c.cid, c.imgs, c.content, c.ctime, m.headimgurl, m.name
			FROM m_comment as c LEFT JOIN m_member as m ON c.mid = m.mid 
			WHERE c.type = '0' AND c.pid = '$gid' ORDER BY c.cid DESC";
	
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$data = $res->fetchAll();
	//print_r($data);die;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>项目详情</title>
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
	<div class="header_back"><a href="#"></a></div>
    <h2>项目详情</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<?php include_once ('../template/item_detail_top.php');?> 
    <div class="detail mt10 mb10">
		<ul class="detail_ul">
        	<?php if($data):;?>
        	<?php foreach($data as $v):;?>
        	<li class="detail_comment pt10 pb10 white">
            	<div class="person">
                    <div class="person_img"><img src="<?php echo $v['headimgurl'];?>" /></div>
                    <div class="person_name"><?php echo $v['name'];?></div>
                    <div class="time"><?php date('Y-m-d H:i:s', $v['ctime']);?></div>
                    <!--精华<div class="best"></div>-->
                </div>
                <div class="cm">
                	<div class="cm_img">
                    	<?php $imgs =  explode('|', $v['imgs']);?>
                        <?php foreach($imgs as $vv):;?>
                        <img src="<?php echo IMG_PATH . $vv;?>" width="50%" />
                        <?php endforeach;?>
                    </div>
                    <div class="cm_ct">
                		<?php echo $v['content'];?>
                	</div>
                </div>
                <div class="cl"></div>
            </li>
           <?php endforeach;?> 
           <?php else:?>
           <div>该项目暂无评论，快抢沙发！</div>
           <?php endif;?>	
        </ul>    
    </div>
    <div class="comment_button"><button onclick="window.location.href='act_comment.php?gid=<?php echo $gid;?>'">我要评论</button>
  </div>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
