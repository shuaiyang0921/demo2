<?php 
  include_once('../includes/init.php'); 
  //判断是否登录
  $admin = checkAdmin();

  // 判断是否有权限
  checkAuth();
  // 查找管理员权限角色
  $sql = "SELECT auth.*,admin.groupid FROM {$pre_}auth_group AS auth LEFT JOIN {$pre_}admin AS admin ON auth.id = admin.groupid WHERE admin.id = ".$admin['id'];
  $group = $db->find($sql);

  // 查找管理员角色下面的权限规则
  /*$ruleid = $group['rules'];
  $sql = "SELECT * FROM {$pre_}auth_rule WHERE id IN ($ruleid) ORDER BY id ASC";*/
  $sql = "SELECT * FROM {$pre_}auth_rule ORDER BY id ASC";
  $rulesList = $db->select($sql);

  // 调用递归函数 无限级导航栏
  $rulesList = getTree($rulesList,'pid');

  // 接收删除数据
  $delete = isset($_POST['delete']) ? $_POST['delete'] : false;

  // 删除规则
  if($delete){

    // 把规则ID转换成字符串并保存到变量
    $ruleid = implode(',',$delete);
    // 执行删除语句
    $ruleidStr = $db->delete('auth_rule',"id IN ($ruleid)");
    // 返回执行结果
    if($ruleidStr){
      showMsg('删除规则成功','rulelist.php');
    }else{
      showMsg('删除规则失败','rulelist.php');
    }
  }

  // 编辑规则
  $action = isset($_GET['action']) ? $_GET['action'] : false;
  // 获取id，判断是否传值与重复
  $editid = isset($_POST['editid']) ? $_POST['editid'] : 0;
  // 如果有传值从数据库中查询相关字段
  if($action == 'did'){
    $sql = "SELECT * FROM {$pre_}auth_rule WHERE id = ".$_GET['did'];
    $ruleStr = $db->find($sql);
    // 传回json数据并结束
    echo json_encode($ruleStr);
    exit;
  }

  // 如果有id值
  if($editid){
    // 把name和title保存出来，用于查询数据库是否有重复规则
    $name = $_POST['name'];
    $title = $_POST['title'];
    $pid = $_POST['pid'];
    $sql = "SELECT * FROM {$pre_}auth_rule WHERE id != $editid AND (name = '$name' OR title = '$title')";
    $repeat = $db->find($sql);
    // 如果有重复不给编辑
    if($repeat){
      showMsg('此规则已存在','rulelist.php');
      exit;
    }

    // 查找pid是否存在
    $sql = "SELECT * FROM {$pre_}auth_rule WHERE id = $pid";
    $pidSrt = $db->find($sql);
    // 不存在不给添加
    if(!$pidSrt && $pid != 0){
      showMsg('规则归属不存在','rulelist.php');
      exit;
    }

    // pid = 0 显示，= 1 隐藏
    $ismenu = $pid == 0 ? 1 : 0;

    // 组装数据
    $data = array(
      "pid"=>$pid,
      "name"=>$name,
      "title"=>$title,
      "status"=>$_POST['status'],
      "ismenu"=>$ismenu
    );
    // 调用类函数，执行更新语句
    $ruleedit = $db->update($data,'auth_rule',"id = $editid");
    if($ruleedit){
      showMsg('规则编辑成功','rulelist.php');
    }else{
      showMsg('规则编辑失败','rulelist.php');
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
            <h1 class="page-title">规则管理</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="btn-toolbar">
                  <?php if(checkAuth('ruleadd.php',false)){ ?>
                    <button class="btn btn-primary" onClick="location='ruleadd.php'"><i class="icon-plus"></i>添加规则</button>
                  <?php } ?>
                </div>
                <div class="well">
                    <table class="table treetable" id="ruleList">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>规则标题</th>
                          <th>规则状态</th>
                          <th>是否显示</th>
                          <th>操作</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach($rulesList as $item){ ?>
                        <tr data-tt-id="<?php echo $item['id'] ?>" data-tt-parent-id="<?php echo $item['pid'];?>">
                          <td><input type="checkbox" name="delete[]" value="<?php echo $item['id']; ?>"></td>
                          <td><?php echo $item['title'] ?></td>
                          <td><?php echo $item['status'] ? "启用" : "禁用";?></td>
                          <td><?php echo $item['ismenu'] ? "显示" : "隐藏"; ?></td>
                          <td>
                            <?php if(checkAuth('ruleedit.php',false)){ ?>
                              <a href="#editid" class="edit" data-editid="<?php echo $item['id']; ?>" data-toggle="modal">编辑&nbsp;&nbsp;</a>
                            <?php } ?>
                            <?php if(checkAuth('ruledelete.php',false)){ ?>
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
                        <p class="error-text"><i class="icon-warning-sign modal-icon"></i>确定删除规则吗？</p>
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
                        <h3 id="myModalLabel">规则编辑</h3>
                    </div>
                    <form action="" method="post">
                      <div class="modal-body">
                          <label>规则归属</label>
                          <input type="number" name="pid" required value="" >
                          <label>规则地址</label>
                          <input type="text" name="name" required value="" >
                          <label>规则标题</label>
                          <input type="text" name="title" required value="" >
                          <label>规则状态</label>
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
    $("#ruleList").treetable({ expandable: true });


    // 多选
    // 获取父级规则复选框
    var choic = $("tr[data-tt-parent-id='0'] input");
    // 反选函数 点击事件
    choic.on('click',function(){
      // 获取复选框父元素tr
      var parent = $(this).parents('tr').attr("data-tt-id");
      // 根据tr查找子规则复选框
      var son = $(this).parents('tr').siblings(`tr[data-tt-parent-id=${parent}]`).children().children("input");
      // 循环逐个反选
      for(var i = 0;i < son.length; i++){
        son[i].checked = !son[i].checked;
      }
    })


    // 给编辑按钮绑定点击事件
    $(".edit").bind("click",function(){
      // 获取编辑按钮属性值
      var editid = $(this).attr("data-editid");
      // 调用ajax函数
      getAjsx("action=did&did="+editid,'rulelist.php',ruleedit);
    });

    // 编辑规则回调函数 把接收到的参数绑定到弹窗
    function ruleedit(data){
      $("input[name='editid']").val(data.id);
      $("input[name='pid']").val(data.pid);
      $("input[name='name']").val(data.name);
      $("input[name='title']").val(data.title);
      $("select[name='status']").val(data.status);
    }
</script>




<!-- 弹窗组件 -->
<!-- <link href="../assets/admin/LayX/layx.min.css" rel="stylesheet" type="text/css" />
<script src="../assets/admin/LayX/layx.min.js" type="text/javascript"></script> -->
