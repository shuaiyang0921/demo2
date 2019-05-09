<?php 
  include_once('../includes/init.php'); 

  // 判断是否登录
  $admin = checkAdmin();
  // 判断是否有权限
  checkAuth();
  
  if($_POST){
    // 把相关信息保存到变量
    $pid = $_POST['pid'];
    $title = $_POST['title'];
    $name = $_POST['name'];
    // 查找pid是否存在
    $sql = "SELECT * FROM {$pre_}auth_rule WHERE id = $pid";
    $pidSrt = $db->find($sql);
    // 不存在并且!=0不给添加
    if($pidSrt || $pid == 0){
      // 查找规则是否存在
      $sql = "SELECT * FROM {$pre_}auth_rule WHERE name = '$name' OR title = '$title'";
      $rule = $db->find($sql);
      // 已存在不给添加
      if($rule){
        showMsg('此规则已存在','ruleadd.php');
      }

      $ismenu = $pid == 0 ? 1 : 0;

      // 组装数据
      $data = array(
        'pid'=>$pid,
        'name'=>$name,
        'title'=>$title,
        'status'=>$_POST['status'],
        'ismenu'=>$ismenu
      );
      $ruleadd = $db->add($data,'auth_rule');

      if($ruleadd){
        showMsg('规则添加成功','rulelist.php');
      }else{
        showMsg('规则添加失败','ruleadd.php');
      }
    }else{
      showMsg('规则归属不存在','ruleadd.php');
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
            <h1 class="page-title">添加规则</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">添加规则</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                    
                <div class="btn-toolbar">
                    <button class="btn btn-primary" onClick="location='rulelist.php'"><i class="icon-list"></i> 规则列表</button>
                  <div class="btn-group">
                  </div>
                </div>

                <div class="well">
                    <div id="myTabContent" class="tab-content">
                      <div class="tab-pane active in" id="home">
                        <form method="post">
                            <!-- <label>文章分类</label>
                            <select name="cate" class="input-xlarge">
                              <option value="">新闻</option>
                              <option value="">新闻</option>
                              <option value="">新闻</option>
                            </select> -->
                            <label>规则归属</label>
                            <input type="number" name="pid" required value="" class="input-xxlarge">
                            <label>规则地址</label>
                            <input type="text" name="name" required value="" class="input-xxlarge">
                            <label>规则标题</label>
                            <input type="text" name="title" required value="" class="input-xxlarge">
                            <label>规则状态</label>
                            <select name="status" class="input-xlarge">
                              <option value="1">启用</option>
                              <option value="0">禁用</option>
                            </select>
                            <!-- <label>是否显示</label>
                            <input type="text" value="" class="input-xxlarge">
                            <label>规则标题</label>
                            <textarea value="Smith" rows="3" class="input-xxlarge"></textarea> -->
                            <label></label>
                            <input class="btn btn-primary" type="submit" value="提交" />
                        </form>
                      </div>
                  </div>
                </div>

                <div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel">Delete Confirmation</h3>
                  </div>
                  <div class="modal-body">
                    
                    <p class="error-text"><i class="icon-warning-sign modal-icon"></i>Are you sure you want to delete the user?</p>
                  </div>
                  <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <button class="btn btn-danger" data-dismiss="modal">Delete</button>
                  </div>
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


