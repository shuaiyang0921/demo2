<?php 
  include_once('../includes/init.php'); 

  // 判断是否登录
  $admin = checkAdmin();
  // 判断是否有权限
  checkAuth();

  // 查找权限组
  $sql = "SELECT * FROM {$pre_}auth_group";
  $groupStr = $db->select($sql);
  
  if($_POST){
    
    // 把相关信息保存到变量
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 查找pid是否存在
    $sql = "SELECT * FROM {$pre_}admin WHERE username = '$username'";
    $adminSrt = $db->find($sql);
    if($adminSrt){
      showMsg('此账号已存在','adminadd.php');
    }

    // 调用函数生成密码盐
    $salt = getRandomStr();

    // 组装数据
    $data = array(
      'username'=>$username,
      'password'=>md5($password.$salt),
      'salt'=>$salt,
      'register_time'=>time(),
      'status'=>$_POST['status'],
      'groupid'=>$_POST['groupid']
    );

    //头像上传 如果上传成功返回文件名 失败false
    if($_FILES['avatar']['error'] == 0)
    {
      // 调用文件上传函数  表单name名，保存地址
      $avatar = uploads("avatar","../assets/uploads/admin");
      if($avatar)
      {
        // 判断图片是否存在，存在就将其删掉
        @is_file("../assets/".$user['avatar']) && @unlink("../assets/".$user['avatar']);

        // 把新头像地址保存到数据库
        $data["avatar"] = "uploads/admin/$avatar";
      }
    }


    /*var_dump($_POST);echo '<br />';var_dump($data);die;*/
    $adminadd = $db->add($data,'admin');

    if($adminadd){
      showMsg('管理员添加成功','adminlist.php');
    }else{
      showMsg('管理员添加失败','adminadd.php');
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once('meta.php'); ?>
  </head>

  <body>     
    <?php include_once('header.php'); ?>
    <?php include_once('menu.php'); ?>

    <div class="content">
        <div class="header">
            <h1 class="page-title">添加管理员</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">添加管理员</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                    
                <div class="btn-toolbar">
                    <button class="btn btn-primary" onClick="location='adminlist.php'"><i class="icon-list"></i> 管理员列表</button>
                  <div class="btn-group">
                  </div>
                </div>

                <div class="well">
                  <div id="myTabContent" class="tab-content">
                    <div class="tab-pane active in" id="home">
                      <form method="post" enctype="multipart/form-data">
                          <label>账号</label>
                          <input type="text" name="username" required value="" class="input-xlarge">
                          <label>密码</label>
                          <input type="text" name="password" required value="" class="input-xlarge">
                          <label>头像</label>
                          <input type="file" name="avatar" id="file" value="" class="input-xxlarge">
                          <label>权限组</label>
                          <select name="groupid" class="input-xlarge">
                            <?php foreach($groupStr as $item){ ?>
                              <option value="<?php echo $item['id'] ?>"><?php echo $item['title'] ?></option>
                            <?php } ?>
                          </select>
                          <label>权限状态</label>
                          <select name="status" class="input-xlarge">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                          </select>
                          <label></label>
                          <input class="btn btn-primary" type="submit" value="提交" />
                      </form>
                    </div>
                  </div>
                </div>
                <footer>
                    <hr>
                    <p>&copy; 2017 <a href="#">copyright</a></p>
                </footer>
                    
            </div>
        </div>
    </div>    
  </body>
</html>


