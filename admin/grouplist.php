<?php 
  include_once('../includes/init.php'); 
  //判断是否登录
  $admin = checkAdmin();
  // 判断是否有权限
  checkAuth();


  // 查找角色列表
  $sql = "SELECT auth.* FROM {$pre_}auth_group AS auth";
  $ruleRow = $db->select($sql);

  // 接收删除数据
  $delete = isset($_POST['delete']) ? $_POST['delete'] : false;

  // 删除角色
  if($delete){
//			var_dump($delete);exit;
    // 把角色ID转换成字符串并保存到变量
    $groupid = implode(',',$delete);
	
    // 执行删除语句
    $groupidStr = $db->delete('auth_group',"id IN ($groupid)");

    // 返回执行结果
    if($groupidStr){
      showMsg('删除角色成功','grouplist.php');
    }else{
      showMsg('删除角色失败','grouplist.php');
    }
  }

  // 查找权限组
  $sql = "SELECT * FROM {$pre_}auth_group";
  $groupStr = $db->select($sql);

  // 查找权限列表
  $sql = "SELECT id,title FROM {$pre_}auth_rule";
  $ruleStr = $db->select($sql);

  // 编辑角色
  $action = isset($_GET['action']) ? $_GET['action'] : false;
  // 获取id，判断是否传值与重复
  $editid = isset($_POST['editid']) ? $_POST['editid'] : 0;
  // 如果有传值从数据库中查询相关字段
  if($action == 'did'){
    $sql = "SELECT * FROM {$pre_}auth_group WHERE id = ".$_GET['did'];
    $groupStr = $db->find($sql);
    // 传回json数据并结束
    echo json_encode($groupStr);
    exit;
  }

  // 如果有id值
  if($editid){
    
    // 把name和title保存出来，用于查询数据库是否有重复角色
    $title = $_POST['title'];
    $rules = empty($_POST['rules']) ? array(1) : $_POST['rules'];
    $rules = implode(',',$rules);
    $sql = "SELECT * FROM {$pre_}auth_group WHERE id != $editid AND title = '$title'";
    $repeat = $db->find($sql);
    // 如果有重复不给编辑
    if($repeat){
      showMsg('此角色已存在','grouplist.php');
      exit;
    }

    // 组装数据
    $data = array(
      "title"=>$title,
      "groupid"=>$_POST['groupid'],
      "status"=>$_POST['status'],
    );

    // 调用类函数，执行更新语句
    $ruleedit = $db->update($data,'group',"id = $editid");
    if($ruleedit){
      showMsg('角色编辑成功','grouplist.php');
    }else{
      showMsg('角色编辑失败','grouplist.php');
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
            <h1 class="page-title">角色管理</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="btn-toolbar">
                  <?php if(checkAuth('groupadd.php',false)){ ?>
                    <button class="btn btn-primary" onClick="location='groupadd.php'"><i class="icon-plus"></i>添加角色</button>
                  <?php } ?>
                </div>
                <div class="well">
                    <table class="table treetable" id="grouplist">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>角色名称</th>
                          <th>权限状态</th>
                          <th style="width:200px;">权限列表</th>
                          <th>操作</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach($ruleRow as $item){ ?>
                        <tr>
                          <td><input type="checkbox" name="delete[]" value="<?php echo $item['id']; ?>"></td>
                          <td><?php echo $item['title'] ?></td>
                          <td><?php echo $item['status'] ? "启用" : "禁用";?></td>
                          <td><?php echo $item['rules']; ?></td>
                          <td>
                            <?php if(checkAuth('groupedit.php',false)){ ?>
                              <a href="#editid" class="edit" data-editid="<?php echo $item['id']; ?>" data-toggle="modal">编辑&nbsp;&nbsp;</a>
                            <?php } ?>
                            <?php if(checkAuth('groupdelete.php',false)){ ?>
                              <a href="#myModal" role="button" onclick="cut(this)" data-ruleid="<?php echo $item['id']; ?>"  data-toggle="modal">删除</a>
                            <?php } ?>
                          </td>
                        </tr>
                      <?php } ?>
                      <tr><td colspan="5" class="multiple">
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
                        <p class="error-text"><i class="icon-warning-sign modal-icon"></i>确定删除角色吗？</p>
                    </div>
                    <form action="" method="post">
                      <div class="modal-footer">
                          <input type="hidden" name="delete[]" value="">
                          <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
                          <button class="btn btn-danger">删除</button>
                      </div>
                    </form>
                </div>

                <div class="modal small hide fade" style="top:35%;width:480px;" id="editid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">角色编辑</h3>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="modal-body">
                        <label>角色名称</label>
                          <input type="text" name="title" required value="" class="input-xlarge">
                          <label>权限状态</label>
                          <select name="status" class="input-xlarge">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                          </select>
                          <label>权限列表</label>
                          <ul style="column-count:3;">
                          <?php foreach ($ruleStr as $value) { ?>
                            <li class="nav"><input id="rules<?php echo $value['id']; ?>" type="checkbox" <?php echo $value['id'] == 1 ? 'checked' : ''; ?> name="rules[]" value="<?php echo $value['id'] ?>">
                            <label  style="display: inline-block;" for="rules<?php echo $value['id']; ?>"><?php echo $value['title'] ?></label></li>
                          <?php } ?>
                          </ul>
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

    // 给编辑按钮绑定点击事件
    $(".edit").bind("click",function(){
      // 获取编辑按钮属性值
      var editid = $(this).attr("data-editid");
      // 调用ajax函数
      getAjax("action=did&did="+editid,'grouplist.php',groupedit);
    });

    // 编辑角色回调函数 把接收到的参数绑定到弹窗
    function groupedit(data){
      $("input[name='editid']").val(data.id);
      $("input[name='title']").val(data.title);
      $("select[name='status']").val(data.status);
      var rules = data.rules.split(',');
      var long = rules.length;
      var input = $("input[name='rules[]']");
      var inputLong = input.length;
      // for(var i = 0;i < inputLong; i++){
      //   input[i].checked = false;
      // }
      for(var i = 0;i < inputLong; i++){
        input[i].checked = false;
        for(var j = 0;j < long;j++){
          if(input[i].value == rules[j]){
            input[i].checked = true;
          }
        }
      }
      console.log(rules);
    }
</script>
<!-- ,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38 -->