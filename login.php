<?php
include_once('./includes/init.php');

//退出操作
if(isset($_GET['action']) && $_GET['action'] == "logout")
{
	//删除掉cookie 一个是时间过期 值为空
	setcookie('user',null,time()-12);
	showMsg("退出成功","login.php");
	exit;
}

if($_POST)
{
	$username = trim($_POST['username']);

	$sql = "SELECT * FROM {$pre_}user WHERE username = '$username'";

	$user = $db->find($sql);

	if(!$user)
	{
		showMsg("用户不存在","login.php");
		exit;
	}

	//验证密码
	$salt = $user['salt'];
	$password = md5(trim($_POST['password']).$salt);

	if($user['password'] != $password)
	{
		showMsg("该用户密码错误","login.php");
		exit;
	}

	//更新最后登录时间
	$update = array(
		"last_login_time"=>time()
	);

	$affect = $db->update($update,"user","id = ".$user['id']);

	if($affect)
	{
		//成功登录，保存用户信息
		$arr = array(
			"id"=>$user['id'],
			"username"=>$user['username'],
			"avatar"=>$user['avatar']
		);
		setcookie("user",json_encode($arr),time()+3600+24);
		showMsg("登录成功","user.php");
		exit;
	}else{
		showMsg("登录失败","user.php");
		exit;
	}
}



?>
<!DOCTYPE html>
<html>
	<head>
		<?php include_once('meta.php');?>
	</head>

	<body>
		<iframe src="head.php" scrolling="no" width="100%" height="65px"></iframe>
		<div class="main layui-clear">
			<div class=" layui-container fly-marginTop">
				<div class="fly-panel fly-panel-user" pad20="">
					<div class="layui-tab layui-tab-brief" lay-filter="user">
						<ul class="layui-tab-title">
							<li class="layui-this">登入</li>
							<li>
								<a href="register.php">注册</a>
							</li>
						</ul>
						<div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
							<div class="layui-tab-item layui-show">
								<div class="layui-form layui-form-pane">
									<form method="post">
										<div class="layui-form-item"> <label for="L_email" class="layui-form-label">用户名</label>
											<div class="layui-input-inline"> <input type="text" id="userName" name="username" required=""  placeholder="请输入用户名" lay-verify="required" autocomplete="off" class="layui-input"> </div>
										</div>
										<div class="layui-form-item"> <label for="L_pass" class="layui-form-label">密码</label>
											<div class="layui-input-inline"> <input type="password" id="L_pass" name="password"  placeholder="请输入密码" required="" lay-verify="required" autocomplete="off" class="layui-input"> </div>
										</div>
										<div class="layui-form-item"> <button class="layui-btn" lay-filter="*" lay-submit="">立即登录</button> <span style="padding-left:20px;"> 

									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>

</html>