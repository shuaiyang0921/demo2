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
$sql = "SELECT count(*) as c from {$pre_}post where $where";
$count = $db->find($sql);

$limit = 5;
$size = 6;

$pageStr = page($page,$count['c'],$limit,$size);

//数据
$data_start = ($page-1)*$limit;
$sql = "SELECT * FROM {$pre_}post WHERE $where ORDER BY id desc LIMIT $data_start,$limit";
$paylist = $db->select($sql);


$sql = "SELECT * FROM {$pre_}config WHERE title = 'pointPay'";
$pointConf = $db->find($sql);





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
        <li class="layui-this" lay-id="info">消费记录</li>
      </ul>
      <div class="layui-form layui-tab-content" style="padding: 20px 0;">
        
      
      <form method="get">
       开始时间：<input type="date" name="start" value="<?php echo $start ? date('Y-m-d',$start) : '';?>" /> 
       结束时间：<input type="date" name="end" value="<?php echo $end ? date('Y-m-d',$end) : '';?>" /> 
       <button class="layui-btn">查询</button>
        <table class="layui-table">
          <thead>
            <tr>
              <th>消费积分</th>
              <th>消费时间</th>
              <!--<th>查看</th>-->
            </tr> 
          </thead>
          <tbody>
            <?php foreach($paylist as $item){?>
            <tr>
              <td><?php echo $item['point'];?></td>
              <td><?php echo date("Y-m-d H:i",$item['register_time']);?></td>
              
              <!--<td>
                <a href="pay_info.php?payid=<?php echo $item['id'];?>">查看详细</a>
              </td>-->
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
  arr[4].classList.add('layui-this');
</script>