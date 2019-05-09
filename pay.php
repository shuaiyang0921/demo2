<?php
include_once("./includes/init.php");

//检测用户是否登录 如果没有登录就跳转
$user = checkUser();

//页码值
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = isset($_GET['start']) ? strtotime($_GET['start']) : 0;
$end = isset($_GET['end']) ? strtotime($_GET['end']) : 0;

$where = "userid = ".$user['id'];

if($start && $end)
{
  $where .= " AND BETWEEN $start AND $end";
}else if($start)
{
  $where .= " AND register_time >= $start";
}else if($end)
{
  $where .= " AND register_time <= $end";
}

//总条数
$sql = "SELECT count(*) as c from {$pre_}user_pay where $where";
$count = $db->find($sql);

$limit = 5;
$size = 6;

$pageStr = page($page,$count['c'],$limit,$size);

//数据
$data_start = ($page-1)*$limit;
$sql = "SELECT * FROM {$pre_}user_pay WHERE $where ORDER BY id desc LIMIT $data_start,$limit";
$paylist = $db->select($sql);


$sql = "SELECT * FROM {$pre_}config WHERE title = 'pointPay'";
$pointConf = $db->find($sql);

if($_POST)
{
  $point = $_POST['point'];

  $data = array(
    "point"=>$point*$pointConf['value'],
    "userid"=>$user['id'],
    "register_time"=>time(),
    "status"=>0,
  );

  $insertid = $db->add($data,"user_pay");

  if($insertid)
  {
    showMsg("积分充值记录提交成功，等待管理员审核","pay.php");
    exit;
  }else{
    showMsg("积分充值失败","pay.php");
    exit;
  }
}



?>
<!DOCTYPE html>
<html>
<head>
<?php include_once('meta.php');?> 
</head>
<body>

<iframe src="head.php" scrolling="no" width="100%" height="65px" ></iframe>

<div class="layui-container fly-marginTop fly-user-main">
  <?php include_once('user_menu.php');?>
  
  <div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
      <ul class="layui-tab-title" id="LAY_mine">
        <li class="layui-this" lay-id="info">充值</li>
      </ul>
      <div class="layui-form layui-tab-content" style="padding: 20px 0;">
        <div class="layui-form layui-form-pane layui-tab-item layui-show">
            <div class="layui-form-item">
              <label for="username" class="layui-form-label">用户名</label>
              <div class="layui-input-inline">
                <input type="text" id="username" name="username" required lay-verify="username" autocomplete="off" value="<?php echo $user['username'];?>" class="layui-input" form="user" disabled />
              </div>
            </div>
            <div class="layui-form-item">
              <label for="point" class="layui-form-label">充值金额</label>
              <div class="layui-input-inline">
                <input type="number" form="user" id="point" name="point" data-pointconf="<?php echo $pointConf['value'];?>" onchange="payPoint(this)"  required placeholder="请输入充值金额" value="" class="layui-input">
              </div>
              <div id="username-notice" style="color:red;" class="layui-form-mid layui-word-aux">兑换比率：1/<?php echo $pointConf['value'];?></div>
            </div>
            <div class="layui-form-item">
              <label for="repoint" class="layui-form-label">对应积分</label>
              <div class="layui-input-inline">
                <input type="number" id="repoint" name="repoint"   value="0" class="layui-input" disabled />
              </div>
            </div>
          </div>
          
          <div class="layui-form-item">
            <form method="post" enctype="multipart/form-data" id="user">
              <button class="layui-btn">确认充值</button>
            </form>
          </div>
        </div>

        <br />
      
      <form method="get">
       开始时间：<input type="date" name="start" value="<?php echo $start ? date('Y-m-d',$start) : '';?>" /> 
       结束时间：<input type="date" name="end" value="<?php echo $end ? date('Y-m-d',$end) : '';?>" /> 
       <button class="layui-btn">查询</button>
        <table class="layui-table">
          <thead>
            <tr>
              <th>充值积分</th>
              <th>充值时间</th>
              <th>充值状态</th>
              <th>查看</th>
            </tr> 
          </thead>
          <tbody>
            <?php foreach($paylist as $item){?>
            <tr>
              <td><?php echo $item['point'];?></td>
              <td><?php echo date("Y-m-d H:i",$item['register_time']);?></td>
              <?php if($item['status'] == 1){?>
                <td style="color:red;">审核通过</td>
              <?php }else if($item['status'] == 0){?>
                <td style="color:red;">正在审核</td>
              <?php }else if($item['status'] == -1){?>
                <td style="color:red;">审核未通过</td>
              <?php } ?>
              <td>
                <a href="pay_info.php?payid=<?php echo $item['id'];?>">查看详细</a>
              </td>
            </tr>
            <?php }?>
          </tbody>
        </table>
        
        <?php echo $pageStr;?>
      </form>
      
      
      
      
      </div>
    </div>
  </div>
</div>

</body>
</html>
<script>
  function payPoint(obj)
  {
    var pointConf = obj.dataset.pointconf;
    var money = obj.value;

    var res = parseInt(money*pointConf);

    document.getElementById("repoint").value = res;
  }
  var arr = document.getElementsByClassName('layui-nav-item');
  arr[0].classList.remove('layui-this');
  arr[6].classList.add('layui-this');
</script>