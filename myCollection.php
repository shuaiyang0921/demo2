<?php
include_once("./includes/init.php");

//检测用户是否登录 如果没有登录就跳转
$user = checkUser();

$page = isset($_GET['page']) ? $_GET['page'] : 1;



$count_where = "userid = {$user['id']}";
$data_where = "favorite.userid = {$user['id']}";

$sql = "SELECT COUNT(*) AS c FROM {$pre_}favorite WHERE $count_where";
$count = $db->find($sql);

$limit = 6;
$size = 4;

$pageStr = page($page,$count['c'],$limit,$size);

//查询数据
$start = ($page-1)*$limit;
$sql = "SELECT favorite.*,post.title,ifnull(comment.comment_count,0) AS cont  
FROM {$pre_}favorite AS favorite
LEFT JOIN {$pre_}post AS post 
ON favorite.postid = post.id 
LEFT JOIN (SELECT postid,COUNT(1) AS comment_count FROM {$pre_}comment group by postid )comment 
ON post.id = comment.postid
WHERE $data_where ORDER BY post.id desc LIMIT $start,$limit";

$favoritelist = $db->select($sql);
//var_dump($favoritelist);exit;



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
          <li data-type="mine-jie" lay-id="index" class="layui-this">我的收藏</li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
          <div class="layui-tab-item layui-show">
            <ul class="mine-view jie-row">
            	<?php foreach($favoritelist as $item){?>
              <li>
                <a class="jie-title" href="postinfo.php?postid=<?php echo $item['id'];?>" target="_blank">
                	<?php echo $item['title'];?></a>
                	<i><?php echo date("Y-m-d H:i",$item['register_time']);?></i>
                <div class="fly-list-hint">
									<i class="iconfont" title="回答">&#xe60c;</i>
									<?php echo $item['cont'];?>
								</div>
              </li>
              <?php }?>
            </ul>
            <div style="text-align: center">
							<?php echo $pageStr;?>
						</div>
            <div id="LAY_page"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<script type="text/javascript">
	var arr = document.getElementsByClassName('layui-nav-item');
	arr[0].classList.remove('layui-this');
	arr[3].classList.add('layui-this');
</script>