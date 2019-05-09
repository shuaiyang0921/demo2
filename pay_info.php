<?php
include_once("./includes/init.php");

//检测用户是否登录 如果没有登录就跳转
$user = checkUser();

$payid = isset($_GET['payid']) ? $_GET['payid'] : 0;

$sql = "SELECT * FROM {$pre_}user_pay WHERE id = $payid";
$info = $db->find($sql);

if(!$info)
{
  showMsg("充值记录不存在请重新查询","pay.php");
  exit;
}

//积分比率
$sql = "SELECT * FROM {$pre_}config WHERE title = 'pointPay'";
$pointConf = $db->find($sql);



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
        readonlyMode:true
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
        <li class="layui-this" lay-id="info">充值信息</li>
      </ul>
      <div class="layui-form layui-tab-content" style="padding: 20px 0;">
        <div class="layui-form layui-form-pane layui-tab-item layui-show">
            <div class="layui-form-item">
              <label for="point" class="layui-form-label">充值余额</label>
              <div class="layui-input-inline">
                <input disabled type="text" id="point" name="point" value="<?php echo $info['point']/$pointConf['value'];?>" class="layui-input" form="user">
              </div>
            </div>
            <div class="layui-form-item">
              <label for="point" class="layui-form-label">充值积分</label>
              <div class="layui-input-inline">
                <input disabled type="text" id="point" name="point" value="<?php echo $info['point'];?>" class="layui-input" form="user">
              </div>
            </div>
            <div class="layui-form-item">
              <label for="phone" class="layui-form-label">充值时间</label>
              <div class="layui-input-inline">
                <input disabled type="text" form="user" id="phone" name="phone" autocomplete="off" value="<?php echo date('Y-m-d H:i',$info['register_time']);?>" class="layui-input">
              </div>
            </div>
            <div class="layui-form-item">
              <label for="phone" class="layui-form-label">充值状态</label>
              <div class="layui-input-inline">
                <?php if($info['status'] == 1){?>
                  <input disabled type="text" form="user" name="phone" autocomplete="off" value="审核通过" class="layui-input">
                <?php }else if($info['status'] == 0){?>
                  <input disabled type="text" form="user" name="phone" autocomplete="off" value="正在审核" class="layui-input">
                <?php }else if($info['status'] == -1){?>
                  <input disabled type="text" form="user" value="审核未通过" class="layui-input">
                <?php } ?>
              </div>
            </div>

            <?php if($info['status'] == -1){?>
            <div class="layui-form-item layui-form-text">
              <div class="layui-input-block">
                <textarea form="user" id="content" name="content" class="layui-textarea" style="height: 80px;"><?php echo $info['content'];?></textarea>
              </div>
            </div>
            <?php }?>

          </div>
          
          
          <div class="layui-form-item">
              <a href="pay.php" class="layui-btn">返回</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>