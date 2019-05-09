<?php
header("Content-Type:text/html;charset=utf-8");
error_reporting(E_ALL); //设置报错级别
date_default_timezone_set("PRC");
session_start();  //开启session会话

//引入函数库
include_once('helpers.php');

//引入数据库类
include_once("db.php");

//数据库全局变量
$db = new DB("localhost","root","root","ask");

//表前缀全局变量
$pre_ = $db->prefix;



//判断用户是否登录
function checkUser($url = 'login.php')
{	
    global $pre_;
    global $db;
	//会员中心的每个页面都要判断一下是否登录
	$_USER = isset($_COOKIE['user']) ? json_decode($_COOKIE['user'],true): false;

	if(!$_USER)
	{
		if($url)
		{
			showMsg("登录失败，请重新登录","login.php");
			exit;
		}
		
	}else{
		$userid = isset($_USER['id']) ? $_USER['id'] : 0;
		$username = isset($_USER['username']) ? $_USER['username'] : '';

		$sql = "SELECT * FROM {$pre_}user WHERE id = $userid AND username = '$username'";
		$info = $db->find($sql);

		if(!$info)
		{
			if($url)
			{
				//如果登录失败走进来
				//如果cookie被伪造了那要清空他
				setcookie("user",null,time()-1234567);
				showMsg("登录失败，请重新登录","login.php");
				exit;
			}
		}

		return $_USER;
	}

}


//判断管理员是否登录
function checkAdmin($url = 'login.php')
{	
    global $pre_;
    global $db;
	//会员中心的每个页面都要判断一下是否登录
	$_ADMIN = isset($_SESSION['admin']) ? json_decode($_SESSION['admin'],true): false;

	if(!$_ADMIN)
	{
		if($url)
		{
			showMsg("登录失败，请重新登录","login.php");
			exit;
		}
		
	}else{
		$adminid = isset($_ADMIN['id']) ? $_ADMIN['id'] : 0;
		$username = isset($_ADMIN['username']) ? $_ADMIN['username'] : '';

		$sql = "SELECT * FROM {$pre_}admin WHERE id = $adminid AND username = '$username'";
		$info = $db->find($sql);

		if(!$info)
		{
			if($url)
			{
				//如果登录失败走进来
				//如果session被伪造了那要清空他
				$_SESSION['admin'] = null;
				session_destroy(); //销毁会话 里面所有的数据都会被销毁
				showMsg("登录失败，请重新登录","login.php");
				exit;
			}
		}


		//判断该管理员的角色组是否存在
		$sql = "SELECT auth.*,admin.groupid FROM {$pre_}admin AS admin LEFT JOIN {$pre_}auth_group AS auth ON admin.groupid = auth.id WHERE admin.id = ".$adminid;

		$group = $db->find($sql);
	
		if(!$group || empty($group['rules']))
		{
			if($url)
			{
				//如果登录失败走进来
				//如果session被伪造了那要清空他
				$_SESSION['admin'] = null;
				session_destroy(); //销毁会话 里面所有的数据都会被销毁
				showMsg("该管理员无任何权限","login.php");
				exit;
			}
		}

		return $_ADMIN;
	}

}

//判断管理员是否有权限 ruleName 权限名称 location是否跳转 true跳转
function checkAuth($ruleName = null,$location=true)
{
	global $pre_;
    global $db;
	if(!$ruleName)
	{
		$ruleName = basename($_SERVER['PHP_SELF']);
	}
	

	$sql = "SELECT id,status FROM {$pre_}auth_rule WHERE name = '$ruleName'";
	$currentRule = $db->find($sql);
//	var_dump($sql);exit;
	if(!$currentRule)
	{
		if($location)
		{
			showMsg("该用户权限不存在");
			exit;
		}else{
			return false;
		}
		
	}else if(!$currentRule['status'])
	{
		if($location)
		{
			showMsg("该权限已经被禁用");
			exit;
		}else{
			return false;
		}
		
	}

	$currentId = $currentRule['id'];

	// 1
	// 1,2,3,4,11
	// 2,3,4,1,11,12
	// 2,3,4,11,12,1

	$sql = "SELECT id FROM {$pre_}auth_group WHERE rules LIKE '$currentId' OR rules LIKE '$currentId,%' OR rules LIKE '%,$currentId' OR rules LIKE '%,$currentId,%'";
	$useRule = $db->find($sql);

	if(!$useRule)
	{
		if($location)
		{
			showMsg("该用户无权限");
			exit;
		}else{
			return false;
		}
		
	}else{
		return true;
	}
}

?>