<?php 
  include_once('../includes/init.php');

  // 退出登录
  $logout = isset($_GET['action']) ? $_GET['action'] : 0;
  // 不为空且相等
  if(!empty($logout) && $logout == "logout"){
    // 删除session
    $_SESSION['admin'] = null;
    // 销毁会话
    session_destroy();
    // 清空cookie
    setcookie('remember',null,time()-100);
    // 跳转登录页面
    showMsg('退出成功','index.php');
    exit;
  }

  // 管理员设置记住密码
  $remember = isset($_COOKIE['remember']) ? $_COOKIE['remember'] : 0;
  if($remember){
    // 查找有权限的管理员
    $sql = "SELECT id,username FROM {$pre_}admin WHERE status = 1";
    $userlist = $db->select($sql);

    foreach($userlist as $item){
      $info = md5($item['id'].$item['username']);

      // 匹配管理员信息
      if($info == $remember){
        $arr = array(
            "id"=>$item['id'],
            "username"=>$item['username']
        );
        $_SESSION['admin'] = json_encode($arr);
        // 跳转
        header("LOcation:homepage.php");
        exit;
      }
    }
  }

  if($_POST){
    if($_SESSION['authcode'] != $_POST['imgcode']){
      showMsg('验证码不正确','index.php');exit;
    }

    $username = $_POST['username'];

    $sql = "SELECT * FROM {$pre_}admin WHERE username = '$username'";
    $user = $db->find($sql);

    if(!$user){
      showMsg('账号不存在，请重新输入','index.php');
      exit;
    }

    $password = md5($_POST['password'].$user['salt']);

    if($password != $user['password']){
      showMsg('密码错误，请重新输入','index.php');
      exit;
    }

    //判断管理员角色组是否存在-->是否有权限
    $sql = "SELECT auth.*,admin.groupid,admin.status AS adminStatus FROM {$pre_}admin AS admin LEFT JOIN {$pre_}auth_group AS auth ON admin.groupid = auth.id WHERE admin.id = ".$user['id'];
    $auth = $db->find($sql);

    if(!$auth || $auth['status'] == 0 || empty($auth['rules']) || $auth['adminStatus'] == 0){
      showMsg('该管理员无任何权限','index.php');
      exit;
    }

    //更新最后登录时间
    $update = array(
      "register_time"=>time()
    );
    $affect = $db->update($update,'admin',"id = ".$user['id']);

    if($affect){
      //登录成功，保存用户信息
      $arr = array(
        "id"=>$user['id'],
        "username"=>$user['username']
      );


      // 判断是否选择记住密码
      $remember = isset($_POST['remember']) ? $_POST['remember'] : 0;
      if($remember){
        // 加密登录信息
        $info = md5($user['id'].$user['username']);
        // 登录信息保存一天
        /*setcookie('remember',$info,time()*3600*24);*/
        setcookie('remember',$info,time()+3600*24);
      }

      // 保存用户信息到session
      $_SESSION['admin'] = json_encode($arr);
      showMsg('登录成功','homepage.php');
      exit;
    }else{
      showMsg('登录失败','index.php');
      exit;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once('meta.php'); ?>
  </head>

  <body>   
    <div class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="###"><span class="second">Admin</span></a>
        </div>
    </div>

    <div class="row-fluid">
        <div class="dialog">
            <div class="block">
                <p class="block-heading">登录</p>
                <div class="block-body">
                    <form method="post">
                        <label for="username">账号</label>
                        <input name="username" id="username" type="text" class="span12" required placeholder="请输入账号" >
                        <label for="password">密码</label>
                        <input name="password" id="password" type="password" class="span12" placeholder="请输入密码" required >
                        <label for="imgcode">验证码</label>
                        <label for="imgcode"><img style="border:1px solid #aaa;" src="imgcode.php" onclick="this.src='imgcode.php'" alt=""></label>
                        <input name="imgcode" type="text" id="imgcode" class="span12" placeholder="请输入验证码" required >
                        <label for="remember">
                          <input name="remember" value="num" type="checkbox" id="remember"> 记住密码
                        </label>
                        <button class="btn btn-primary pull-right">登录</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>


