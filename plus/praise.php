<?php
require_once '../weixin/test2.php';
require_once '../include/function.php';

//if(IS_POST){
    /*
   if($_POST['vcode']<>$_SESSION['vcodes']){
	   header("location:/index.php?g=Wap&m=Vote&a=over&token=$this->_post('token')&id=$this->_get('id')&wecha_id=$this->_get('wecha_id')");
	   exit;
   }
   */
   //echo 1;
   $datas['typeid']      	= $_POST['typeid'];
   $datas['item_id']       	= $_POST['item_id'];
   $datas['openid']      	= $_POST['open_id'];
   $datas['time']   		= time();
   $data=get_row('id,typeid.item_id,openid','m_dianzan','typeid ='.$datas['typeid'].' AND item_id ='.$datas['item_id'].' AND openid =\''.$datas['openid'].'\'' );
   if(empty($data)){
       $res=insert('m_dianzan','typeid,item_id,openid,time',"$datas[typeid],$datas[item_id],$datas[openid],$datas[time]");
       echo 1;	 
       exit;
    }else{
        echo 0;
        exit;
    }

//}


?>