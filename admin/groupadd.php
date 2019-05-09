<?php 
  include_once('../includes/init.php'); 

  // 判断是否登录
  $admin = checkAdmin();
  // 判断是否有权限
  checkAuth();

  // 查找权限列表
  $sql = "SELECT id,title FROM {$pre_}auth_rule";
  $ruleStr = $db->select($sql);
  
  if($_POST){
    
    // 把相关信息保存到变量
    $title = $_POST['title'];
    $rules = empty($_POST['rules']) ? array(1) : $_POST['rules'];
    $rules = implode(',',$rules);

    // 查找角色是否存在
    $sql = "SELECT * FROM {$pre_}auth_group WHERE title = '$title'";
    $groupSrt = $db->find($sql);
    if($groupSrt){
      showMsg('此角色已存在','groupadd.php');
    }

    // 组装数据
    $data = array(
      'title'=>$title,
      'status'=>$_POST['status'],
      'rules'=>$rules
    );


    /*var_dump($_POST);echo '<br />';var_dump($data);die;*/
    $adminadd = $db->add($data,'auth_group');

    if($adminadd){
      showMsg('角色添加成功','grouplist.php');
    }else{
      showMsg('角色添加失败','groupadd.php');
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
            <h1 class="page-title">添加角色</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">添加角色</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                    
                <div class="btn-toolbar">
                    <button class="btn btn-primary" onClick="location='grouplist.php'"><i class="icon-list"></i> 角色列表</button>
                  <div class="btn-group">
                  </div>
                </div>

                <div class="well">
                  <div id="myTabContent" class="tab-content">
                    <div class="tab-pane active in" id="home">
                      <form method="post" enctype="multipart/form-data">
                          <label>角色名称</label>
                          <input type="text" name="title" required value="" class="input-xlarge">
                          <label>权限状态</label>
                          <select name="status" class="input-xlarge">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                          </select>
                          <label>权限列表</label>
                          <ul style="column-count:4;">
                          <?php foreach ($ruleStr as $value) { ?>
                            <li class="nav"><input id="rules<?php echo $value['id']; ?>" type="checkbox" <?php echo $value['id'] == 1 ? 'checked' : ''; ?> name="rules[]" value="<?php echo $value['id'] ?>">
                            <label  style="display: inline-block;" for="rules<?php echo $value['id']; ?>"><?php echo $value['title'] ?></label></li>
                          <?php } ?>
                          </ul>
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


