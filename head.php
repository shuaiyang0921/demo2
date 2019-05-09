<?php
include_once("./includes/init.php");

$headUser = checkUser(null);


?>
<!DOCTYPE html>
<html>
	<head>
		<?php include_once('meta.php');?>
	</head>
	<body>
		<div class="header">
			<div class="main">
				<a class="title" href="index.php" target="_parent" title="浦江"> 
					<i class="iconfont icon-jiaoliu layui-hide-xs" style="font-size: 22px;"></i>
					你问我答社区</a>
				<div class="nav">
					<a class="nav-this" target="_parent" href="index.php">
						<i class="iconfont icon-wenda"></i>你问我答</a>
				</div>
				<div class="nav-user">

					<?php if($headUser){?>
					<a class="avatar" href="user.php" target="_parent">
					
						<img src="<?php echo empty($headUser['avatar']) ? './assets/home/images/uer.jpg' : './assets/'.$headUser['avatar']; ?>" />
						<cite>
							<?php echo $headUser['username'];?>
						</cite>
					</a>
					<div class="nav">
						<a target="_parent" href="login.php?action=logout"><i class="iconfont icon-tuichu" style="top: 0; font-size: 22px;"></i>退出</a>
					</div>

					<?php }else{ ?>

						<a target="_parent" href="login.php" class="iconfont icon-touxiang layui-hide-xs" style="margin-top: 4px; display: inline-block;">
						</a>
						
						<div class="nav"  style="font-size:14px;color: white;margin-top: -5px;margin-left: 1px; ">
							<a target="_parent" href="login.php"  target="_parent" >登录</a>
							<a target="_parent" href="register.php" target="_parent" >注册</a>
						</div>

					<?php }?>
					 
				</div>
			</div>
		</div>
	</body>
</html>