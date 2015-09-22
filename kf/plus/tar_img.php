<?php
$imgsrc="aa.gif";
$percent_num=70;
//$imgsrc="aa.gif";
if(!empty($_GET['i'])){
	$imgsrc=$_GET['i'];
}
if(!empty($_GET['p'])){
	$percent_num=$_GET['p'];
}
image_png_size_add($imgsrc,$percent_num);
function image_png_size_add($imgsrc,$percent_num=60)
{ 
  list($width,$height,$type)=getimagesize($imgsrc); 
  $percent = $percent_num/100;  //Í¼Æ¬Ñ¹Ëõ±È
  if($width<=220){$percent=1;}
  $new_width = $width * $percent; 
  $new_height = $height* $percent;
  switch($type){ 
    case 1: 
	  header('Content-Type:image/gif'); 
      $giftype=check_gifcartoon($imgsrc); 
      if($giftype){ 	  
		  $image_wp=imagecreatetruecolor($new_width, $new_height); 
		  $image = imagecreatefromgif($imgsrc); 
		  imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
		  imagejpeg($image_wp); 
		  imagedestroy($image_wp);  
	  }else{
		  $image_wp=imagecreatetruecolor($new_width, $new_height); 
		  $image = imagecreatefromgif($imgsrc); 
		  imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
		  imagejpeg($image_wp); 
		  imagedestroy($image_wp);  		  
	  }
      break; 
    case 2: 
      header('Content-Type:image/jpeg'); 
      $image_wp=imagecreatetruecolor($new_width, $new_height); 
      $image = imagecreatefromjpeg($imgsrc); 
      imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
      imagejpeg($image_wp); 
      imagedestroy($image); 
      imagedestroy($image_wp);  
      break; 
    case 3: 
      header('Content-Type:image/png'); 
      $image_wp=imagecreatetruecolor($new_width, $new_height); 
      $image = imagecreatefrompng($imgsrc); 
      imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
      imagejpeg($image_wp); 
      imagedestroy($image); 
      imagedestroy($image_wp); 
      break; 
    case 4:
      $giftype=check_gifcartoon($imgsrc); 
      if($giftype){ 
        header('Content-Type:image/gif'); 
        $image_wp=imagecreatetruecolor($new_width, $new_height); 
        $image = imagecreatefromgif($imgsrc); 
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
        imagejpeg($image_wp); 
        imagedestroy($image_wp); 
      } 
      break; 	  	  
  } 
} 


function check_gifcartoon($image_file){ 
  $fp = fopen($image_file,'rb'); 
  $image_head = fread($fp,1024); 
  fclose($fp); 
  return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head)?false:true; 
} 
?>