<?php
include_once("./includes/init.php");

if($_POST)
{
	//验证码验证
	if($_SESSION['imgcode'] != trim($_POST['imgcode']))
	{
		showMsg("验证码不一致，请重新输入","register.php");
		exit();
	}

	//判断用户名是否被注册
	$username = trim($_POST['username']);

	$sql = "SELECT * FROM {$pre_}user WHERE username = '$username'";
	$user = $db->find($sql);

	if($user)
	{
		showMsg('该用户已经被注册了','register.php');
		exit;
	}

	//生成密码盐
	$salt = getRandomStr();

	//生成密码
	$password = md5(trim($_POST['password']).$salt);

	//组装数据，插入数据库，并且判断是否有注册成功
	$data = array(
		"username"=>$_POST['username'],
		"salt"=>$salt,
		"password"=>$password,
		"register_time"=>time()
	);

	//执行插入语句
	$insertid = $db->add($data,"user");

	//如果有插入成功，就提醒跳转
	if($insertid)
	{
		$sql = "SELECT id,username,avatar FROM ${pre_}user WHERE id = $insertid";
		$user = $db->find($sql);
		//先将用户信息保存到缓存里面 前台用户用cookie 后台管理员才用session
		//设置cookie 并且将cookie保存1天 如果为0的话 关闭浏览器就没有了
		setcookie("user",json_encode($user),time()+3600*24);
		showMsg("注册成功，跳转会员中心","index.php");
		exit;
	}else{
		showMsg("注册失败","register.php");
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
		<div class="layui-container fly-marginTop">
			<div class="fly-panel fly-panel-user" pad20="">
				<div class="layui-tab layui-tab-brief" lay-filter="user">
					<ul class="layui-tab-title">
						<li>
							<a href="login.php">登入</a>
						</li>
						<li class="layui-this">注册</li>
					</ul>
					<div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
						<div class="layui-tab-item layui-show">
							<div class="layui-form layui-form-pane">
								<form method="post">
									<div class="layui-form-item"> <label for="L_email" class="layui-form-label">用户名</label>
										<div class="layui-input-inline"> <input type="text" id="username" name="username" placeholder="请输入用户名" required lay-verify="required|username"  class="layui-input"> </div>
										<div class="layui-form-mid layui-word-aux">将会成为您唯一的登入名</div>
									</div>
									
									<div class="layui-form-item"> <label for="L_pass" class="layui-form-label">密码</label>
										<div class="layui-input-inline"> <input type="password" id="L_pass" placeholder="请输入密码" name="password" required lay-verify="required|password" class="layui-input"> </div>
										<div class="layui-form-mid layui-word-aux">6到16个字符</div>
									</div>
									<div class="layui-form-item"> <label for="L_repass" class="layui-form-label">确认密码</label>
										<div class="layui-input-inline"> <input type="password" id="L_repass" name="repass" required placeholder="请输入确认密码" lay-verify="required|repass" class="layui-input"> </div>
									</div>
									<div class="layui-form-item"> 
										<label for="imgcode" class="layui-form-label"><img src="imgcode.php" onclick="this.src='imgcode.php?v='+Math.random();" /></label>
										<div class="layui-input-inline"> <input type="text" id="imgcode" name="imgcode" required placeholder="请输入验证码" lay-verify="required" class="layui-input"> </div>
									</div>	
									<div class="layui-form-item"> <button class="layui-btn" lay-filter="register" lay-submit="">立即注册</button> </div>
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
<script>
layui.use('form', function(){
  var form = layui.form;


  form.on('submit(register)',function(data){

	var password = data.field.password;
	form.verify({
		username: function(value, item){ //value：表单的值、item：表单的DOM对象
			if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
			return '用户名不能有特殊字符';
			}
			if(/(^\_)|(\__)|(\_+$)/.test(value)){
			return '用户名首尾不能出现下划线\'_\'';
			}
			if(/^\d+\d+\d$/.test(value)){
			return '用户名不能全为数字';
			}
		},
		password: [
			/^[\S]{6,12}$/
			,'密码必须6到12位，且不能出现空格'
		],
		repass:function(value,item)
		{
			if(value != password)
			{
				return '密码和确认密码不一致';
			}
		}
	});


  });
  
  //各种基于事件的操作，下面会有进一步介绍
      
});
</script>