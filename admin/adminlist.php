<?php 
  include_once('../includes/init.php'); 
  //判断是否登录
  $admin = checkAdmin();
  // 判断是否有权限
  checkAuth();


  // 查找管理员列表
  $sql = "SELECT admin.*,auth.title FROM {$pre_}admin AS admin LEFT JOIN {$pre_}auth_group AS auth ON admin.groupid = auth.id ORDER BY register_time ASC";
  $ruleRow = $db->select($sql);

  /*// 调用子孙层级函数
  $ruleTree = getTree($ruleRow,'pid',0,0);*/

  // 接收删除数据
  $delete = isset($_POST['delete']) ? $_POST['delete'] : false;
	
  // 删除管理员
  if($delete){
//		var_dump($delete);exit;
    // 把管理员ID转换成字符串并保存到变量
    $adminid = implode(',',$delete);
	
    // 执行删除语句
    $adminidStr = $db->delete('admin',"id IN ($adminid)");
    // 返回执行结果
    if($adminidStr){
      showMsg('删除管理员成功','adminlist.php');
    }else{
      showMsg('删除管理员失败','adminlist.php');
    }
  }

  // 查找权限组
  $sql = "SELECT * FROM {$pre_}auth_group";
  $groupStr = $db->select($sql);

  // 编辑管理员
  $action = isset($_GET['action']) ? $_GET['action'] : false;
  // 获取id，判断是否传值与重复
  $editid = isset($_POST['editid']) ? $_POST['editid'] : 0;
  // 如果有传值从数据库中查询相关字段
  if($action == 'did'){
    $sql = "SELECT * FROM {$pre_}admin WHERE id = ".$_GET['did'];
    $adminStr = $db->find($sql);
    // 传回json数据并结束
    echo json_encode($adminStr);
    exit;
  }

  // 如果有id值
  if($editid){
    
    // 把name和title保存出来，用于查询数据库是否有重复管理员
    $username = $_POST['username'];
    $sql = "SELECT * FROM {$pre_}admin WHERE id != $editid AND username = '$username'";
    $repeat = $db->find($sql);
    // 如果有重复不给编辑
    if($repeat){
      showMsg('此管理员已存在','adminlist.php');
      exit;
    }

    // 组装数据
    $data = array(
      "username"=>$username,
      "groupid"=>$_POST['groupid'],
      "status"=>$_POST['status'],
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

    // 调用类函数，执行更新语句
    $ruleedit = $db->update($data,'admin',"id = $editid");
    if($ruleedit){
      showMsg('管理员编辑成功','adminlist.php');
    }else{
      showMsg('管理员编辑失败','adminlist.php');
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once('meta.php'); ?>

    <!-- 层级插件 -->
    <link rel="stylesheet" href="../assets/plugin/treetable/css/jquery.treetable.css" />
    <link rel="stylesheet" href="../assets/plugin/treetable/css/jquery.treetable.theme.default.css" />
    <script src="../assets/plugin/treetable/jquery.min.js"></script>
    <script src="../assets/plugin/treetable/jquery.treetable.js"></script>
  </head>

  <body> 
    <?php include_once('header.php');?>
    
    <?php include_once('menu.php');?>
    <div class="content">
        <div class="header">
            <h1 class="page-title">管理员管理</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="btn-toolbar">
                  <?php if(checkAuth('adminadd.php',false)){ ?>
                    <button class="btn btn-primary" onClick="location='adminadd.php'"><i class="icon-plus"></i>添加管理员</button>
                  <?php } ?>
                </div>
                <div class="well">
                    <table class="table treetable" id="adminlist">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>账号</th>
                          <th>头像</th>
                          <th>添加时间</th>
                          <th>最后登录时间</th>
                          <th>权限状态</th>
                          <th>所属权限组</th>
                          <th>操作</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach($ruleRow as $item){ ?>
                        <tr>
                          <td><input type="checkbox" name="delete[]" value="<?php echo $item['id']; ?>"></td>
                          <td><?php echo $item['username'] ?></td>
                          <td><img width="25" style="border:none;" src="../assets/<?php echo $item['avatar'];?>" alt=""></td>
                          <td><?php echo date('Y-m-d H:i',$item['register_time']); ?></td>
                          <td><?php echo date('Y-m-d H:i',$item['last_login_time']); ?></td>
                          <td><?php echo $item['status'] ? "启用" : "禁用";?></td>
                          <td><?php echo $item['title']; ?></td>
                          <td>
                            <?php if(checkAuth('adminedit.php',false)){ ?>
                              <a href="#editid" class="edit" data-editid="<?php echo $item['id']; ?>" data-toggle="modal">编辑&nbsp;&nbsp;</a>
                            <?php } ?>
                            <?php if(checkAuth('admindelete.php',false)){ ?>
                              <a href="#myModal" role="button" onclick="cut(this)" data-ruleid="<?php echo $item['id']; ?>"  data-toggle="modal">删除</a>
                            <?php } ?>
                          </td>
                        </tr>
                      <?php } ?>
                      <tr><td colspan="8" class="multiple">
                        <?php if(checkAuth('ruledelete.php',false)){ ?>
                          <a class="btn" disabled href="javascript:void(0)" role="button" data-toggle="modal">批量删除</a>
                        <?php } ?>
                      </td></tr>
                      </tbody>
                    </table>
                </div>
                <div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">删除确认</h3>
                    </div>
                    <div class="modal-body">
                        <p class="error-text"><i class="icon-warning-sign modal-icon"></i>确定删除管理员吗？</p>
                    </div>
                    <form action="" method="post">
                      <div class="modal-footer">
                          <input type="hidden" name="delete[]" value="">
                          <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
                          <button class="btn btn-danger">删除</button>
                      </div>
                    </form>
                </div>

                <div class="modal small hide fade" style="top:35%;" id="editid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">管理员编辑</h3>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="modal-body">
                        <label>账号</label>
                        <input type="text" name="username" required value="" class="input-xlarge">
                        <!-- <label>密码</label>
                        <input type="text" name="password" value="" class="input-xlarge"> -->
                        <label>头像</label>
                        <input type="file" name="avatar" id="file" value="" class="input-xxlarge">
                        <label for=""></label>
                        <img id="adavatar" width="50" src="" alt="">
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
                      </div>
      
                      <div class="modal-footer">
                          <input type="hidden" name="editid" value="">
                          <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
                          <button class="btn btn-danger">确定</button>
                      </div>
                    </form>
                </div>

                <footer>
                    <hr>
                    <p>&copy; 2017 <a href="#" target="_blank">copyright</a></p>
                </footer> 
            </div>
        </div>
    </div>
  </body>
</html>

<!-- 公共脚本 -->
<script src="../assets/admin/public.js"></script>

<script type="text/javascript">

    // 初始化层级树
    $("#adminlist").treetable({ expandable: true });

    // 给编辑按钮绑定点击事件
    $(".edit").bind("click",function(){
      // 获取编辑按钮属性值
      var editid = $(this).attr("data-editid");
      // 调用ajax函数
      getAjax("action=did&did="+editid,'adminlist.php',adminedit);
    });

    // 编辑管理员回调函数 把接收到的参数绑定到弹窗
    function adminedit(data){
      $("input[name='editid']").val(data.id);
      $("input[name='username']").val(data.username);
      $("#adavatar").attr('src',"../assets/"+data.avatar);
      $("select[name='groupid']").val(data.groupid);
      $("select[name='status']").val(data.status);
    }
</script>