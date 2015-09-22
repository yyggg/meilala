<?php if(!defined('ROOT')) die('Access denied.');

class Admin extends Auth{
	protected $ajax = array(); //用于ajax数据收集与输出
	public $json; //ajax时的JSON对象

	public function __construct($path = ''){
		parent::__construct($path);

		if($path[1] == 'ajax') { //任意控制器的动作为ajax时, 执行ajax动作, 禁止输出页头, 页尾及数据库访问错误
			APP::$DB->printerror = false; //ajax数据库访问不打印错误信息
			$this->ajax['s'] = 1; //初始化ajax返回数据, s表示状态
			$this->ajax['i'] = ''; //i指ajax提示信息
			$this->ajax['d'] = ''; //d指ajax返回的数据
			$this->json = new JSON;

			if(!$this->admin){//管理员验证不成功, 直接输出ajax信息, 并终止ajax其它程序程序运行
				$this->ajax['i'] = "管理员授权错误! 请确认已成功登录后台.";
				die($this->json->encode($this->ajax));
			}
		}else{
			if($path[1] == 'logout') $this->logout(); //无论哪个控制器, 只要是logout动作, admin用户退出

			if($path[0] == 'online'){
				$this->s_page_header($path); //授权成功, 客服输出页头
			}else{
				$this->page_header($path); //授权成功, 管理员输出页头
			}
		}
	}

	/**
	 * 输出页头 page_header
	 */
	protected function page_header($path = '') {

		$isAdmin = $this->CheckAccess();

		if($path[0] == 'index' AND !$isAdmin) Redirect('online'); //客服人员进入首页时跳转到客服操作页面

		echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>'.APP::$_CFG['Title'].' - 后台管理</title>
<link rel="shortcut icon" href="' . SYSDIR . 'public/img/favicon.ico" type="image/x-icon"> 
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'public/admin.css">
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'public/easyDialog/easyDialog.css">
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'pub
lic/admin_m.css">
<script src="'. SYSDIR .'public/jquery191.js" type="text/javascript"></script>
<script src="'. SYSDIR .'public/admin.js" type="text/javascript"></script>
<script type="text/javascript">
var this_uri = "' . strstr($_SERVER['REQUEST_URI'], 'index.php') . '";
var baseurl = "' . BASEURL . '";
</script>
</head>
<body>
<script src="'. SYSDIR .'public/easyDialog/easyDialog.js" type="text/javascript"></script>
' . Iif($isAdmin, $this->header_menu($path), $this->s_header_menu($path)) . '
<div class="maindiv">
	<div id="main">';
	}

	/**
	 * 输出客服操作页面页头 s_page_header
	 */
	protected function s_page_header($path = '') {
		header_nocache(); //不缓存

		echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>'.APP::$_CFG['Title'].'</title>
<link rel="shortcut icon" href="' . SYSDIR . 'public/img/favicon.ico" type="image/x-icon"> 
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'public/admin.css">
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'public/easyDialog/easyDialog.css">
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'public/jquery.tipTip.css">
<link rel="stylesheet" type="text/css" href="'. SYSDIR .'public/admin_m.css">
<script src="'. SYSDIR .'public/jquery191.js" type="text/javascript"></script>
<script src="'. SYSDIR .'public/jquery.tipTip.js" type="text/javascript"></script>
<script src="'. SYSDIR .'public/jquery.scrollbar.js" type="text/javascript"></script>
<script src="'. SYSDIR .'public/support.js" type="text/javascript"></script>
<script src="'. SYSDIR .'public/admin_m.js" type="text/javascript"></script>
<script type="text/javascript">
var this_uri = "' . strstr($_SERVER['REQUEST_URI'], 'index.php') . '",
BASEURL = "' . BASEURL . '",
SYSDIR = "' . SYSDIR . '",
WS_HOST = "' . WS_HOST . '",
WS_PORT = "' . WS_PORT . '",
update_time = ' . ForceInt(APP::$_CFG['Update']) . ',
admin ={id: ' . $this->admin['aid'] . ', type: ' . $this->admin['type'] . ', sid: "' . $this->admin['sid'] . '", fullname: "' . $this->admin['fullname'] . '", post: "' . $this->admin['post'] . '", agent: "' . $this->admin['agent'] . '"};
</script>
</head>
<body class="online">
<script src="'. SYSDIR .'public/easyDialog/easyDialog.js" type="text/javascript"></script>
' . $this->s_header_menu($path, 1) . '
<div class="maindiv">
<div id="main">';
	}


	//管理员顶部导航菜单内容
	protected function header_menu($path = '') {

		$info_total = 0;

		//如果不是后台首页, 获取cookie统计数据
		if($path[0] != 'index') $info_total = ForceInt(ForceCookieFrom(COOKIE_KEY . 'backinfos'));
 
		return '<div id="header">
	<div class="logo"><a href="./"><img src="'. SYSDIR .'public/img/logo.gif" title="后台首页"></a></div>
	<div id="ajax-loader"></div>
	<div id="topbar">
		<div id="topmenu">
			<dl class="first"></dl>
			<dl class="home">
				<dt><a href="./">首页</a></dt>
				<dd>
					<div>
						<li class="first"><a href="./">首页</a></li>
						<li class="last"></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . BURL('guests') . '">用户</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('users/add') . '">添加客服</a></li>
						<li><a href="' . BURL('users') . '">客服列表</a></li>
						<li><a href="' . BURL('guests') . '">客人管理</a></li>
						<li class="last"><a href="' . BURL('avatar') . '">上传我的头像</a></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . BURL('comments') . '">留言</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('comments') . '">留言列表</a></li>
						<li class="last"></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . BURL('messages') . '">记录</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('messages') . '">记录列表</a></li>
						<li class="last"></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . BURL('phrases') . '">短语</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('phrases/add') . '">添加常用短语</a></li>
						<li class="last"><a href="' . BURL('phrases') . '">常用短语列表</a></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . BURL('settings') . '">系统</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('settings') . '">系统设置</a></li>
						<li><a href="' . BURL('language') . '">语言管理</a></li>
						<li><a href="' . BURL('database') . '">数据维护</a></li>
						<li><a href="' . BURL('phpinfo') . '">环境信息</a></li>
						<li class="last"><a href="' . BURL('upgrade') . '">系统升级</a></li>
					</div>
				</dd>
			</dl>
			<dl class="last"></dl>
		</div>

		<div id="topuser">
			<div class="open"><a href="' . BURL('online') . '" target="_blank" class="link-btn2">进入客服</a></div>
			<dl class="first"></dl>
			<dl class="' . Iif($info_total, 'info', 'info none') . '" id="info_all"><!-- 如果没有信息 class=info none -->
				<dt><a href="' . BURL() . '" title="点击更新提示信息"><i></i><span id="info_total">' . $info_total .  '</span></a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('comments') . '"><font id="info_comms" class="' . Iif($info_total, 'orangeb', 'light') . '">' . $info_total . '</font> 条未读留言</a></li>
						<li class="last"></li>
					</div>
				</dd>
			</dl>
			<dl class="admin">
				<dt><a href="#" class="logout"><i></i></a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . BURL('index/logout') . '"><img src="' . GetAvatar($this->admin['aid']) . '" class="avatar" style="margin-bottom:6px;"><BR><font class=orange>'.$this->admin['fullname'].'</font> 退出?</a></li>
						<li><a href="' . BURL('users/edit?aid=' . $this->admin['aid']) . '">修改我的资料</a></li>
						<li class="last"><a href="' . BURL('avatar') . '">上传我的头像</a></li>
					</div>
				</dd>
			</dl>
			<dl class="last"></dl>
		</div>
		<div></div>
	</div>
</div>';
	}


	//客服顶部导航菜单内容
	protected function s_header_menu($path = '', $blank = 0) {
		if($blank) {
			$blank = ' target="_blank"';
		}else{
			$blank = '';
		}

		$isAdmin = $this->CheckAccess();

		return '<div id="header">
	<div class="logo" ><img src="'. SYSDIR .'public/img/logo.gif"></div>
	<div id="ajax-loader"></div>
	<div id="topbar">
		<div id="topmenu">
			<dl class="first"></dl>
			<dl>
				<dt><a href="' . Iif($isAdmin, BURL('messages'), BURL('mymessages')) . '"' . $blank . '>记录</a></dt>
				<dd>
					<div>
						<li class="first last"><a href="' . Iif($isAdmin, BURL('messages'), BURL('mymessages')) . '"' . $blank . '>对话记录列表</a></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . Iif($isAdmin, BURL('phrases'), BURL('myphrases')) . '"' . $blank . '>短语</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . Iif($isAdmin, BURL('phrases/add'), BURL('myphrases/add')) . '"' . $blank . '>添加常用短语</a></li>
						<li class="last"><a href="' . Iif($isAdmin, BURL('phrases'), BURL('myphrases')) . '"' . $blank . '>常用短语列表</a></li>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><a href="' . Iif($isAdmin, BURL('users/edit?aid=' . $this->admin['aid']), BURL('myprofile')) . '"' . $blank . '>我的</a></dt>
				<dd>
					<div>
						<li class="first"><a href="' . Iif($isAdmin, BURL('users/edit?aid=' . $this->admin['aid']), BURL('myprofile')) . '"' . $blank . '>编辑我的资料</a></li>
						<li class="last"><a href="' . BURL('avatar') . '"' . $blank . '>上传头像</a></li>
					</div>
				</dd>
			</dl>
			<dl class="last"></dl>
		</div>
		<div id="topuser">
			' . Iif($blank, '<div class="open"><a class="link-btn2 set_busy">挂起</a><a class="link-btn3 set_serving" title="解除挂起进入服务状态, 接受新客人加入.">解除挂起</a></div>') . '
			' . Iif($isAdmin, '<div class="open"><a class="link-btn2 reset_socket" title="重启Socket通讯服务, 所有在线客人将丢失. 无特殊原因, 勿重启Socket通讯服务!">重启服务</a></div>') . '
			' . Iif($blank, '<div class="open"><a class="link-btn2 logout">安全退出</a></div>') . '
			<dl class="first"></dl>
			<dl class="supporter"><div>' . $this->admin['fullname'] . ' <label class="grey">[' . $this->admin['post'] . ']</label>&nbsp;&nbsp;<img src="' . GetAvatar($this->admin['aid']) . '" class="avatar w20"></div></dl>
			<dl class="last"></dl>
		</div>
		<div></div>
	</div>
</div>';
	}

	/**
	 * 输出页脚 page_footer
	 */
    protected function page_footer($sysinfo = ''){
		global $sys_starttime;

		$mtime = explode(' ', microtime());
		$sys_runtime = number_format(($mtime[1] + $mtime[0] - $sys_starttime), 3);
		echo '</div>
</div>
<div class="sysinfo">'.date("Y").' &copy; '.APP_NAME.'('.APP_VERSION.') <a href="'.APP_URL.'" target="_blank">iimei.com</a> Done in '.$sys_runtime.' second(s), '.APP::$DB->query_nums.' queries, GMT' .APP::$_CFG['Timezone'].' ' .DisplayDate('', '', 1).'</div>	
<div class="admin_big_img" id="admin_big_img" style="display:none;"></div>
</body>
</html>';
	}

	/**
	 * 析构函数 输出页脚
	 */
	public function __destruct(){
		//登录成功才允许在析构函数中输出面页底部. 未登录时, 有登录页面, 互不冲突
		if($this->admin AND !$this->ajax) $this->page_footer();
	}

}

?>