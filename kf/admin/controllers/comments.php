<?php if(!defined('ROOT')) die('Access denied.');

class c_comments extends Admin{

	public function __construct($path){
		parent::__construct($path);

		$this->CheckAction();
	}


	//快速删除留言
	public function fastdelete(){
		$days = ForceIntFrom('days');

		if(!$days) Error('请选择删除期限!');

		$time = time() - $days * 24 * 3600;

		APP::$DB->exe("DELETE FROM " . TABLE_PREFIX . "comment WHERE readed = 1 AND time < $time");

		Success('comments');
	}

	//批量更新留言
	public function updatecomments(){
		$page = ForceIntFrom('p', 1);   //页码

		if(IsPost('updatecomms')){
			$updatecids = $_POST['updatecids'];

			for($i = 0; $i < count($updatecids); $i++){
				$cid = ForceInt($updatecids[$i]);
				APP::$DB->exe("UPDATE " . TABLE_PREFIX . "comment SET readed = 1 WHERE cid = '$cid'");
			}
		}else{
			$deletecids = $_POST['deletecids'];

			for($i = 0; $i < count($deletecids); $i++){
				$cid = ForceInt($deletecids[$i]);
				APP::$DB->exe("DELETE FROM " . TABLE_PREFIX . "comment WHERE cid = '$cid'");
			}
		}

		Success('comments?p=' . $page);
	}


	public function index(){
		$NumPerPage = 10;
		$page = ForceIntFrom('p', 1);
		$search = ForceStringFrom('s');
		$groupid = ForceStringFrom('g');

		if(IsGet('s')){
			$search = urldecode($search);
		}

		$start = $NumPerPage * ($page-1);

		SubMenu('留言列表', array(array('留言列表', 'comments', 1)));

		TableHeader('搜索及快速删除');
		TableRow('<center><form method="post" action="'.BURL('comments').'" name="searchcomments" style="display:inline-block;*display:inline;"><label>关键字:</label>&nbsp;<input type="text" name="s" size="18">&nbsp;&nbsp;&nbsp;<label>状态:</label>&nbsp;<select name="g"><option value="0">全部</option><option value="1" ' . Iif($groupid == '1', 'SELECTED') . ' class=red>未读</option><option value="2" ' . Iif($groupid == '2', 'SELECTED') . '>已读</option></select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="搜索留言" class="cancel"></form>

		<form method="post" action="'.BURL('comments/fastdelete').'" name="fastdelete" style="display:inline-block;margin-left:80px;*display:inline;"><label>快速删除留言:</label>&nbsp;<select name="days"><option value="0">请选择 ...</option><option value="360">12个月前的已读留言</option><option value="180">&nbsp;6 个月前的已读留言</option><option value="90">&nbsp;3 个月前的已读留言</option><option value="30">&nbsp;1 个月前的已读留言</option></select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="快速删除" class="save" onclick="var _me=$(this);showDialog(\'确定删除所选留言吗?\', \'确认操作\', function(){_me.closest(\'form\').submit();});return false;"></form></center>');
		
		TableFooter();

		if($search){
			if(preg_match("/^[1-9][0-9]*$/", $search)){
				$s = ForceInt($search);
				$searchsql = " WHERE cid = '$s' OR gid = '$s' OR phone LIKE '%$s%' "; //按ID搜索
				$title = "搜索数字为: <span class=note>$s</span> 的留言";
			}else{
				$searchsql = " WHERE (fullname LIKE '%$search%' OR email LIKE '%$search%' OR content LIKE '%$search%') ";
				$title = "搜索: <span class=note>$search</span> 的留言列表";
			}

			if($groupid) {
				if($groupid == 1 OR $groupid == 2){
					$searchsql .= " AND readed = " . Iif($groupid == 1, 0, 1)." ";
					$title = "在 <span class=note>" .Iif($groupid == 1, '未读留言', '已读留言'). "</span> 中, " . $title;
				}
			}
		}else if($groupid){
			if($groupid == 1 OR $groupid == 2){
				$searchsql .= " WHERE readed = " . Iif($groupid == 1, 0, 1)." ";
				$title = "全部 <span class=note>" .Iif($groupid == 1, '未读留言', '已读留言'). "</span> 列表";
			}
		}else{
			$searchsql = '';
			$title = '全部留言列表';
		}

		$getcomments = APP::$DB->query("SELECT * FROM " . TABLE_PREFIX . "comment ".$searchsql." ORDER BY readed ASC, cid DESC LIMIT $start,$NumPerPage");

		$maxrows = APP::$DB->getOne("SELECT COUNT(cid) AS value FROM " . TABLE_PREFIX . "comment ".$searchsql);

		echo '<form method="post" action="'.BURL('comments/updatecomments').'" name="commentsform">
		<input type="hidden" name="p" value="'.$page.'">';

		TableHeader($title.'('.$maxrows['value'].'个)');
		TableRow(array('ID', '状态', '姓名', 'Email', '电话', '留言内容', '<input type="checkbox" id="checkAll2" for="updatecids[]"> <label for="checkAll2">标记已读</label>', 'IP', '留言时间', '<input type="checkbox" id="checkAll" for="deletecids[]"> <label for="checkAll">删除</label>'), 'tr0');

		if($maxrows['value'] < 1){
			TableRow('<center><BR><font class=redb>未搜索到任何留言!</font><BR><BR></center>');
		}else{
			while($comm = APP::$DB->fetch($getcomments)){
				TableRow(array($comm['cid'],
				Iif($comm['readed'], '<font class=grey>已读</font>', '<font class=red>未读</font>'),
				Iif($comm['gid'], '<a title="编辑" href="'.BURL('guests/edit?gid='.$comm['gid']).'">' . "$comm[fullname]</a>", $comm['fullname']),
				Iif($comm['email'], '<a href="mailto:' . $comm['email'] . '">' . $comm['email'] . '</a>'),
				$comm['phone'],
				nl2br($comm['content']),
				Iif(!$comm['readed'], '<input type="checkbox" name="updatecids[]" value="' . $comm['cid'] . '">'),
				$comm['ip'],
				DisplayDate($comm['time'], '', 1),
				'<input type="checkbox" name="deletecids[]" value="' . $comm['cid'] . '">'));
			}

			$totalpages = ceil($maxrows['value'] / $NumPerPage);

			if($totalpages > 1){
				TableRow(GetPageList(BURL('comments'), $totalpages, $page, 10, 's', urlencode($search), 'g', $groupid));
			}

		}

		TableFooter();

		echo '<div class="submit"><input type="submit" name="updatecomms" value="标记已读" class="cancel" style="margin-right:28px"><input type="submit" name="deletecomms" value="删除留言" class="save" onclick="var _me=$(this);showDialog(\'确定删除所选留言吗?\', \'确认操作\', function(){_me.closest(\'form\').submit();});return false;"></div></form>';
	}

} 

?>