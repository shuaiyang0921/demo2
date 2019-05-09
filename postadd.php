<?php
include_once("./includes/init.php");

//检测用户是否登录 如果没有登录就跳转
$user = checkUser();

$userid = $user['id'];

$sql = "SELECT * FROM {$pre_}user WHERE id = $userid";
$user = $db->find($sql);

if($_POST)
{
  //获取悬赏积分
  $point = isset($_POST['point']) ? intval($_POST['point']) : 1;

  //扣减积分
  $user_point = intval($user['point']);

  $res = $user_point - $point;

  if($res < 0)
  {
    showMsg("积分不足，请充值操作","postadd.php");
    exit;
  }


  //执行源生sql语句开启事务
  $db->runSql("start transaction");  //开启事务

  //先减少积分、增加消费记录、插入帖子表
  $data = array(
    "point"=>$res,
  );

  $userUpdate = $db->update($data,"user","id = $userid");


  //增加消费记录
  $logData = array(
    "desc"=>"发布悬赏帖子：".$_POST['title'],
    "userid"=>$userid,
    "point"=>$point,
    "register_time"=>time(),
    "status"=>1
  );

  $logUpdate = $db->add($logData,"user_log");

  //插入帖子表
  $data = array(
    "title"=>$_POST['title'],
    "content"=>$_POST['content'],
    "userid"=>$userid,
    "point"=>$_POST['point'],
    "register_time"=>time(),
    "state"=>0,
  );


  $postUpdate = $db->add($data,"post");

  if(!$userUpdate && !$logUpdate && !$postUpdate)
  {
    //事务回滚
    $db->runSql("ROLLBACK");  
    showMsg("发布悬赏帖子失败","index.php");
    exit;
  }else{
    $addAction = $db->runSql("COMMIT");
    showMsg("发布悬赏帖子成功","index.php");
    exit;
  }

}

?>
<!DOCTYPE html>
<html>
<head>
  <?php include('meta.php');?>
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

<iframe  src="head.php" scrolling="no" width="100%" height="65px" ></iframe>

<div class="main layui-clear">
  <div class="fly-panel" pad20>
    <h2 class="page-title">发布悬赏</h2>
  

    <div class="layui-form layui-form-pane">
      <form method="post">
        <div class="layui-form-item">
          <label for="L_title" class="layui-form-label">标题</label>
          <div class="layui-input-block">
            <input type="text" id="L_title" name="title" required lay-verify="required" autocomplete="off" class="layui-input">
          </div>
        </div>
        <div class="layui-form-item layui-form-text">
          <div class="layui-input-block">
              <label for="L_content" class="layui-form-label" style="top: -2px;">描述</label>
             <div class="editor">
    			      <textarea id="content" name="content" style="width:1040px;height:450px;visibility:hidden;"></textarea>
  			     </div>
          </div>
          
        </div>
        <div class="layui-form-item">
          <label for="L_title" class="layui-form-label">悬赏</label>
          <div class="layui-input-block">
            <input type="number" min="1"  name="point" required lay-verify="required" placeholder="请输入悬赏的积分数量" class="layui-input">
          </div>
        </div>
        <div class="layui-form-item">
          <button class="layui-btn" lay-filter="*" lay-submit>立即发布</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>