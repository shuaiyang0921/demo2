<?php
header("Content-Type:text/html;charset=utf-8");
include_once("./includes/init.php");

//检测用户是否登录 如果没有登录就跳转
$user = checkUser();

$sql = "SELECT * FROM ${pre_}user WHERE id = ".$user['id'];
$user = $db->find($sql);


if($_POST)
{
  $action = isset($_POST['action']) ? $_POST['action'] : false;
  $username = isset($_POST['username']) ? $_POST['username'] : "";
 
  //这个判断主要是给ajax判断用户名是否存在的
  if($action == "checkuser")
  {
    $id = $user['id'];
    $sql = "SELECT * FROM {$pre_}user WHERE id != $id AND username = '$username'";
    $info = $db->find($sql);
    if($info)
    {
      //已经存在了
      echo json_encode(false);
      exit;
    }else{
      echo json_encode(true);
      exit;
    }
  }
  
	if(!empty($_POST['password']) && !empty($_POST['repass']) && !empty($_POST['nowpass'])){
	  //先判断当前密码是否为空
	  $nowpass = md5($_POST['nowpass'].$user['salt']);
	  if(!empty($_POST['nowpass']) && $nowpass != $user['password'] )
	  {
	  
	    showMsg("当前密码输入有误","set.php");
	    exit;
	  }
	
	  //密码和确认密码
	  if((!empty($_POST['password']) || !empty($_POST['repass'])) && $_POST['password'] != $_POST['repass'] )
	  {
	    showMsg("密码和确认密码不一致","set.php");
	    exit;
	  }
	
			//当每次修改密码的时候，盐都要重新生成
		  $salt = getRandomStr();
		  //生成密码
			$password = md5(trim($_POST['password']).$salt);
	}else{
		$password = $user['password'];
		$salt = $user['salt'];
	}
  
  
  

  //组装数据
  $data = array(
    "username"=>$_POST['username'],
    "password"=>$password,
    "salt"=>$salt,
    "sex"=>$_POST['sex'],
    "phone"=>$_POST['phone'],
    "content"=>$_POST['content'],
  );
//var_dump($_FILES);exit;
  //头像 如果上传成功返回文件名 失败false
  if($_FILES['avatar']['error'] == 0)
  {
  	
    $avatar = uploads("avatar","./assets/uploads");
		 
    if($avatar)
    {
      @is_file("assets/".$user['avatar']) && @unlink("assets/".$user['avatar']);
      $data["avatar"] = "uploads/$avatar";
    }
  }


  $affectid = $db->update($data,"user","id = ".$user['id']);

  if($affectid)
  {
    //更新一下cookie
    $sql = "SELECT id,username,avatar FROM ${pre_}user WHERE id = ".$user['id'];
		$user = $db->find($sql);
		setcookie("user",json_encode($user),time()+3600*24);
		showMsg("更新用户成功！","set.php");
		exit;
  }else{
		showMsg("更新用户失败！","set.php");
		exit;
  }
}


?>
<!DOCTYPE html>
<html>
<head>
<?php include_once('meta.php');?> 
  <link rel="stylesheet" href="./assets/plugin/kindeditor/themes/default/default.css" />
  <script src="./assets/plugin/kindeditor/kindeditor-min.js"></script>
  <script src="./assets/plugin/kindeditor/lang/zh_CN.js"></script>
  <script>
    var editor;
    KindEditor.ready(function(K){
      editor =  K.create('textarea[name="content"]', {
        afterBlur: function(){this.sync();},
      });
    });
  </script>
</head>
<body>

<iframe src="head.php" scrolling="no" width="100%" height="65px" ></iframe>

<div class="layui-container fly-marginTop fly-user-main">
  <?php include_once('user_menu.php');?>
  
  <div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
      <ul class="layui-tab-title" id="LAY_mine">
        <li class="layui-this" lay-id="info">我的资料</li>
        <li lay-id="avatar">头像</li>
        <li lay-id="pass">密码</li>
      </ul>
      <div class="layui-form layui-tab-content" style="padding: 20px 0;">
        <div class="layui-form layui-form-pane layui-tab-item layui-show">
            <div class="layui-form-item">
              <label for="username" class="layui-form-label">用户名</label>
              <div class="layui-input-inline">
                <input type="text" id="username" name="username" required 
                	lay-verify="username" autocomplete="off" 
                	value="<?php echo $user['username'];?>" class="layui-input" form="user">
              </div>
              <div id="username-notice" style="display:none;color:red;" class="layui-form-mid layui-word-aux">该用户名已存在</div>
            </div>
            <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">性别</label>
              <div class="layui-inline">
                <div class="layui-input-inline">
                  <input type="radio" form="user" name="sex" value="1" <?php echo $user['sex'] ? "checked":"";?> title="男">男
                  <input type="radio" form="user" <?php echo $user['sex'] ? "":"checked";?> name="sex" value="0" title="女">女
                </div>
              </div>
            </div>
            <div class="layui-form-item">
              <label for="phone" class="layui-form-label">手机号码</label>
              <div class="layui-input-inline">
                <input type="text" form="user" id="phone" name="phone" autocomplete="off" required lay-verify="phone" value="<?php echo $user['phone'];?>" class="layui-input">
              </div>
            </div>
            <div class="layui-form-item layui-form-text">
              <div class="layui-input-block">
                <textarea form="user" id="content" name="content" class="layui-textarea" style="height: 80px;"><?php echo $user['content'];?></textarea>
              </div>
            </div>
          </div>
          
          <div class="layui-form layui-form-pane layui-tab-item">
            <div class="layui-form-item">
              <div class="avatar-add" style="text-align:center;padding-top:10px;">
                <p>建议尺寸168*168，支持jpg、png、gif，最大不能超过50KB</p>
                <button type="button" class="layui-btn" onclick="document.getElementById('avatar').click();">
                  上传头像
                </button>
                <input style="display:none;" type="file" id="avatar" name="avatar" form="user" />
                <?php if(@is_file("assets/".$user['avatar'])){?>
                  <img onclick="document.getElementById('avatar').click();" src="<?php echo "assets/".$user['avatar'];?>">
                <?php }else{ ?>
                  <img onclick="document.getElementById('avatar').click();" src="https://tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg">
                <?php }?>
                <span class="loading"></span>
              </div>
            </div>
          </div>
          
          <div class="layui-form layui-form-pane layui-tab-item">
              <div class="layui-form-item">
                <label for="nowpass" class="layui-form-label">当前密码</label>
                <div class="layui-input-inline">
                  <input type="password" id="nowpass" name="nowpass" lay-verify="" form="user" placeholder="为空不修改密码" class="layui-input">
                </div>
              </div>
              <div class="layui-form-item">
                <label for="password" class="layui-form-label">新密码</label>
                <div class="layui-input-inline">
                  <input type="password" id="password" name="password" form="user" lay-filter="paasword"   lay-verify="password" placeholder="为空不修改密码" class="layui-input">
                </div>
              </div>
              <div class="layui-form-item">
                <label for="repass" class="layui-form-label">确认密码</label>
                <div class="layui-input-inline">
                  <input type="password" id="repass" name="repass"  lay-verify="repass" form="user" placeholder="为空不修改密码" class="layui-input">
                </div>
              </div>
          </div>
          
          <div class="layui-form-item">
            <form method="post" enctype="multipart/form-data" id="user">
              <button class="layui-btn" lay-filter="register" lay-submit>确认修改</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<script>
layui.cache.page = 'user';
layui.cache.user = {
  username: '游客'
  ,uid: -1
  ,avatar: './assets/home/images/uer.jpg'
  ,experience: 83
  ,sex: '男'
};
layui.config({
  version: "2.0.0"
  ,base: 'assets/home/mods/'
}).extend({
  fly: 'index'
}).use('fly');


layui.use(['form','jquery'], function(){
  var form = layui.form;

  var $ = layui.$;

  $("#username").bind("change",function(){
    $.ajax({
      url:"set.php",
      type:"post",
      dataType:"json",
      data:`action=checkuser&username=`+$(this).val(),
      success:function(data)
      {
        if(data)
        {
          //可以修改的
          $("#username-notice").css("display","none");
          
        }else{
          //不能修改
          $("#username-notice").css("display","inline-block");
          return false;
        }
      }
    })
  });

  form.on('submit(register)',function(data){

    //判断密码和确认密码是否一致
    var password = data.field.password.trim();
    var repass = data.field.repass.trim();
    var nowpass = data.field.nowpass.trim();

    if(nowpass.length)
    {
      if( (password.length || repass.length) && (password != repass) )
      {
        alert('密码和确认密码不一致，请重新填写');
        return false;
      }
    }
    

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
        }
    });


  });
  
  //各种基于事件的操作，下面会有进一步介绍
      
});
var arr = document.getElementsByClassName('layui-nav-item');
  arr[0].classList.remove('layui-this');
  arr[1].classList.add('layui-this');
</script>