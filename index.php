<?php
include_once("includes/init.php");

	
//当前页码值
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$finish = isset($_GET['finish']) ? $_GET['finish'] : -1;
$state = isset($_GET['state']) ? $_GET['state'] : -1;
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";


$count_where = "userid != 0";	
$data_where = "post.userid != 0";

if($finish != -1)
{
	$count_where .= " AND finish = $finish";
	$data_where .= " AND post.finish = $finish";
}

if($state != -1)
{
	$count_where .= " AND state = $state";
	$data_where .= " AND post.state = $state";
}

if(!empty($keyword))
{
	$count_where .= " AND title LIKE '%$keyword%'";
	$data_where .= " AND post.title LIKE '%$keyword%'";
}

//查询总数
$sql = "SELECT COUNT(*) AS c FROM {$pre_}post WHERE $count_where";
$count = $db->find($sql);

$limit = 10;

$size = 5;

$pageStr = page($page,$count['c'],$limit,$size);

//查询数据
$start = ($page-1)*$limit;
$sql = "SELECT post.*,user.username,user.avatar,ifnull(comment.comment_count,0) AS cont 
FROM {$pre_}post AS post LEFT JOIN {$pre_}user AS user ON post.userid = user.id 
LEFT JOIN (SELECT postid,COUNT(1) AS comment_count FROM {$pre_}comment group by postid )comment 
ON post.id = comment.postid WHERE $data_where ORDER BY post.state desc,post.id desc LIMIT $start,$limit";

$postlist = $db->select($sql);


?>
<!DOCTYPE html>
<html>
	<head>
		<?php include_once('meta.php');?>
	</head>
	<body>
		<iframe src="head.php" scrolling="no" width="100%" height="65px" ></iframe>
		<div class="main layui-clear">
			<div class="wrap">
				<div class="content" style="margin-right:0">
					<div class="fly-tab">	
						<span id="menu">
							<a href="index.php" class="<?php echo $finish == -1 && $state == -1 ? 'tab-this' : '' ?>">全部</a>
							<a href="index.php?finish=0" class="<?php echo $finish == 0?'tab-this' : '' ?>">未结帖</a>
							<a href="index.php?finish=1" class="<?php echo $finish == 1 ? 'tab-this' : '' ?>">已采纳</a>
							<a href="index.php?state=2" class="<?php echo $state == 2 ? 'tab-this' : '' ?>">置顶帖</a>
							<a href="index.php?state=1" class="<?php echo $state == 1 ? 'tab-this' : '' ?>">精帖</a>
						</span>
						<form method="get" class="fly-search">
							<i class="iconfont icon-sousuo"></i>
							<input class="layui-input" autocomplete="off" placeholder="搜索内容" 
								type="text" name="keyword" value="<?php echo $keyword;?>" />
						</form>
						<a href="postadd.php" class="layui-btn jie-add">发布悬赏</a>
					</div>

					<ul class="fly-list">
						<?php foreach($postlist as $item){?>
							<li class="fly-list-li">
								<a href="postinfo.php?postid=<?php echo $item['id'];?>" class="fly-list-avatar">
									<?php if(@is_file("assets/".$item['avatar'])){?>
										<img src="assets/<?php echo $item['avatar'];?>" alt="">
									<?php }else{ ?>
										<img src="assets/home/images/uer.jpg" alt="">
									<?php }?>
								</a>
								<h2 class="fly-tip">
									<a href="postinfo.php?postid=<?php echo $item['id'];?>"><?php echo $item['title'];?></a>
									<?php if($item['state'] == 1){?>
										<span class="fly-tip-stick">置顶</span>
									<?php }else if($item['state'] == 2){?>
										<span class="fly-tip-stick">精帖</span>
									<?php }?>
								</h2>
								<p>
									<span><a href="javascript:void(0)"><?php echo $item['username'];?></a></span>
									<span><?php echo date("Y-m-d H:i",$item['register_time']);?></span>
									<span class="fly-list-hint"> 
										<i class="iconfont" title="回答">&#xe60c;</i><?php echo $item['cont'];?>条评论
									</span>
								</p>
							</li>
						<?php }?>
						
					</ul>

					<div style="text-align: center">
						<?php echo $pageStr;?>
					</div>

				</div>
			</div>
		</div>
	</body>

</html>
<!--<script src="../company/assets/lib/jquery-1.8.1.min.js" type="text/javascript" charset="utf-8"></script>
<script>
	$(function(){
	    $("#menu a").click(function() {
	        $(this).siblings('a').removeClass('tab-this');
	        $(this).addClass('tab-this'); 
		});
   });
</script>-->