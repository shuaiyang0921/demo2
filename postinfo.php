<?php 
include_once("includes/init.php");

$user = checkUser(null);

$postid = isset($_GET['postid']) ? $_GET['postid'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 0;
$id = isset($_POST['id']) ? $_POST['id'] : 0;
$state = isset($_GET['state']) ? $_GET['state'] : 0;


//获取帖子数据
$sql = "SELECT post.*,user.username,user.avatar,ifnull(comment.comment_count,0) AS cont 
FROM {$pre_}post AS post 
LEFT JOIN {$pre_}user AS user 
ON post.userid = user.id 
LEFT JOIN (SELECT postid,COUNT(1) AS comment_count 
FROM {$pre_}comment group by postid )comment 
ON post.id = comment.postid 
WHERE post.id = $postid";

$postinfo = $db->find($sql);
//var_dump($postinfo);exit;
if(!$postinfo)
{
	showMsg("帖子数据不存在","index.php");
	exit;
}

if($state)
{
	
	if($state == 1){
		$date = array(
			"state"=>1
		);
		$info = $db->update($date,"post","id=".$postid);
		if($info){
			showMsg("置顶成功","postinfo.php?postid=$postid");
			exit;
		}else{
			showMsg("置顶失败","postinfo.php?postid=$postid");
			exit;
		}
	}if($state == 2){
		
		$date = array(
			"state"=>0
		);
		$info = $db->update($date,"post","id=".$postid);
		if($info){
			showMsg("取消置顶成功","postinfo.php?postid=$postid");
			exit;
		}else{
			showMsg("取消置顶失败","postinfo.php?postid=$postid");
			exit;
		}
	}
}
if($user)
{
	$sql = "SELECT * FROM {$pre_}favorite WHERE userid = ".$user['id']." AND postid = ".$postinfo['id'];
	$favorite = $db->find($sql);
}

//获取评论的数据
$sql = "SELECT comment.*,user.username,user.avatar 
FROM {$pre_}comment AS comment 
LEFT JOIN {$pre_}user AS user 
ON comment.userid = user.id 
WHERE comment.postid = $postid 
ORDER BY comment.parentid ASC";
$comment =  $db->select($sql);

//获取无限级评论
$commentlist = getTree($comment,"parentid");
//var_dump(in_array("{$user['id']}",explode(".",rtrim($commentlist[0]['likes'],'.'))));exit;
//点赞状态
//$sql = "SELECT likes from {$pre_}comment AS comment WHERE comment.postid = $postid";
//$likes= $db->select($sql);
//$zan = explode(".",rtrim(implode(",",$likes),"."));
//$uid = $user['id'];
//$dzan = in_array("$uid",$zan);
//
//var_dump($zan);exit;
//近期热议
$sql = "SELECT post.*,user.username,user.avatar,ifnull(comment.comment_count,0) AS cont 
FROM {$pre_}post AS post 
LEFT JOIN {$pre_}user AS user 
ON post.userid = user.id 
LEFT JOIN (SELECT postid,COUNT(1) AS comment_count FROM {$pre_}comment group by postid ) AS comment 
ON post.id = comment.postid 
ORDER BY post.state desc,post.id desc,comment.comment_count desc LIMIT 10";

$hostlist = $db->select($sql);


//接受采纳和删除的请求
if($action)
{
	//采纳
	if($action == "accept")
	{	
		$arr = array(
			"accept"=>$user[id],
			"finish"=>1
		);
		
		// echo json_encode($postinfo['point']) ;exit;

		$info = $db->update($arr,"post","id=".$postid);

		if($info)
		{
			echo json_encode($info);
			exit;
		}else{
			echo json_encode(false);
			exit;
		}
	}
	else if($action == "delete")
	{
		
		$sql = "SELECT * from {$pre_}comment";
		$comment = $db->select($sql);

		//获取删除id
		$pid = array();

		$commentlist = getTree($comment,'parentid');
		
		
		//获取该条评论的阶层  并将该评论的子层评论循环遍历到数组里
		foreach($commentlist as $item)
		{
			if($item['id'] == $id)
			{
				$commentlist = getTree($comment,'parentid',$id,$item['level'],true);
			}

			$com_id[] = $item['id'];
		}
		
		//储存该条评论的所有子层id
		foreach($commentlist as $item)
		{
			$pid[] = $item['id'];
		}

		//储存该评论id
		$pid[] = $id;

		//数组是静态，前面加载有保留，获取原本字符串长度并截取
		$id_Str = implode(',',$pid);

		$id_Str = substr($id_Str,strlen(implode(',',$com_id))+1);

		$info = $db->delete('comment','id IN('.$id_Str.')');
		
		if($info)
		{
			echo json_encode(true);
			exit;
		}else{
			echo json_encode(false);
			exit;
		}

	}
	else if($action == "delete1")
	{
		$del = $db->delete('post','id='.$postid);
		if($del)
		{
			showMsg("删除帖子成功","index.php");
			exit;
		}else{
			showMsg("删除帖子失败","index.php");
			exit;
		}
		
	}
	else if($action == "comment1")
	{
		$favoritedata = array(
			"userid"=>$user['id'],
			"postid"=>$postinfo['id'],
			"register_time"=>time(),
		);
		
		
		$sql = "SELECT * from {$pre_}favorite";
		$fav = $db->add($favoritedata,'favorite');
	}
	else if($action == "comment2")
	{
		$sql = "SELECT * from {$pre_}favorite WHERE userid = ".$user['id'];
		$fav = $db->delete('favorite');
	}
	else if($action == "dzan"){
		//点赞状态
		$sql = "SELECT likes from {$pre_}comment AS comment WHERE id = ".$id;
		$lik = $db->find($sql);
		$za = explode(".",rtrim(implode(",",$lik),"."));
		$usid = $user['id'];
		$dzan = in_array("$usid",$za);
		//取消点赞
		if($dzan){
			unset($za[array_search($usid, $za)]);
			$zans = implode(".", $za).'.';
			
			$arr = array(
				"likes"=>$zans
			);
			$comment = $db->update($arr,"comment","id=".$id);
		}
		//点赞
		else{
			$zani =explode(" ",$user['id'].'.');
			$zanid = array_merge($za, $zani);
			$zans = implode(".", $zanid);
//			echo json_encode($zans);exit;
			$arr = array(
				"likes"=>$zans
			);
			$comment = $db->update($arr,"comment","id=".$id);
		}
		if($comment)
		{
			echo json_encode($comment);
			exit;
		}else{
			echo json_encode(false);
			exit;
		}
	}
}

//接收帖子评论
if($_POST)
{
	$parentid = !empty($_POST['parentid']) ? $_POST['parentid'] : 0;
	$content = isset($_POST['content']) ? $_POST['content'] : "";

	$data = array(
		"postid"=>$postinfo['id'],
		"userid"=>$user['id'],
		"content"=>$content,
		"register_time"=>time(),
		"parentid"=>$parentid
	);
	
	$res = $db->add($data,'comment');
	
	if($res)
	{
		showMsg("回复成功","postinfo.php?postid=$postid");
		exit;
	}else{
		showMsg("回复失败","postinfo.php?postid=$postid");
		exit;
	}
}
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
      });
    });
  </script>
<style type="text/css" rel="stylesheet">
form {
	margin: 0;
}

.editor {
	margin-top: 5px;
	margin-bottom: 5px;
}

.toName{
	border:none;
	background:rgba(0,0,0,0);
}
</style>

</head>
<body>
	<iframe  src="head.php" scrolling="no" width="100%" height="65px" ></iframe>
	<div class="main layui-clear">
		<div class="wrap">
			<div class="content detail">
				<div class="fly-panel detail-box">
					<h1><?php echo $postinfo['title'];?></h1>
					<div class="fly-tip fly-detail-hint" data-id="">
						<?php if($postinfo['state'] == 1){?>
							<span class="fly-tip-stick">置顶帖</span>
						<? }else if($postinfo['state'] == 2){?>
							<span class="fly-tip-stick">精贴</span>
						<?php }?>
						<?php if($postinfo['userid'] == $user['id']){?>
							<?php if($postinfo['state'] == 1){?>	
								<span class="layui-btn layui-btn-mini jie-admin"> 
									<a href="postinfo.php?postid=<?php echo $postid?>&state=2">取消置顶</a>
								</span> 
							<?php }else if($postinfo['state'] == 0){?>
								<span class="jie-admin"> 
									<a href="postinfo.php?postid=<?php echo $postid?>&state=1">点击置顶</a>
								</span> 
							<?php }?>
							<span class="jie-admin" type="del" style="margin-left: 20px;">
								<a onclick="comment(this,'delete1')">删除该帖</a> 
							</span> 
						<?php }?>
						</span>
						<div class="fly-list-hint">
							<i class="iconfont" title="回答">&#xe60c;</i>
							<?php echo $postinfo['cont'];?>
						</div>
					</div>
					<div class="detail-about">
						<a class="jie-user" href="javascript:void(0)">
							<?php if(@is_file("assets/".$postinfo['avatar'])){?>
							 <img src="assets/<?php echo $postinfo['avatar'];?>" alt="头像"> 
							<?php }else{?>
								<img src="assets/home/images/uer.jpg" alt="头像"> 
							<?php }?>
							 <cite><?php echo $postinfo['username'];?><em>
							 	<?php echo date("Y-m-d H:i",$postinfo['register_time']);?>发布</em> </cite>
							 </a>

						<div class="detail-hits" >

						<?php if($postinfo['userid'] == $user['id']){?>
							<?php if($postinfo['finish'] == 1){?>
							 	<span class="layui-btn layui-btn-mini jie-admin">
								 	<a href="#">已完帖，无法编辑</a> 
								</span> 
							<?php }else{?>
								<span class="layui-btn layui-btn-mini jie-admin">
								 	<a href="detail.php?postid=<?php echo $postid?>">编辑</a> 
								</span> 
							<?php }?>
						<?php }?>

						<?php if($user){?>
								<?php if($favorite){?>
									<span class="layui-btn layui-btn-mini jie-admin  layui-btn-danger" 
										type="collect" data-type="add"> 
										<a onclick="comment(this,'comment2')">取消收藏</a> 
									</span>
								<?php }else{?>
									<span class="layui-btn layui-btn-mini jie-admin" type="collect" data-type="add">	
										<a  onclick="comment(this,'comment1')">收藏</a> 
											<!--id="collectPost"  -->
									</span> 
								<?php }?>
							<?php }?>
						</div>
					</div>
					<div class="detail-body photos" style="margin-bottom: 20px;">
						<?php echo $postinfo['content'];?>
					</div>
				</div>
				<div class="fly-panel detail-box" style="padding-top: 0;">
					<a name="comment"></a>
					<ul class="jieda photos" id="jieda">

					<?php if($commentlist){?>
						<?php foreach($commentlist as $item){?>
							<li style="padding-left:<?php echo $item['level']*20;?>px;" data-id="12" class="jieda-daan">
								<a name="item-121212121212"></a>
								<div class="detail-about detail-about-reply">
									<a class="jie-user" href="#"> 
										<?php if(@is_file("assets/".$postinfo['avatar'])){?>
											<img src="assets/<?php echo $postinfo['avatar'];?>" alt="头像"> 
										<?php }else{?>
											<img src="assets/home/images/uer.jpg" alt="头像"> 
										<?php }?>
										<cite> 
											<i><?php echo $item['username']; ?></i>
											<?php if($item['userid'] == $postinfo['userid']){?>
												<em>(楼主)</em>
											<?php }?>
										</cite> 
									</a>
									<div class="detail-hits">
										<span><?php echo date("Y-m-d H:i",$item['register_time']);?></span>
									</div>
									<?php if($item['id'] == $postinfo['accept']){?>
										<i class="iconfont icon-caina" title="最佳答案"></i>
									<?php }?>
								</div>
								<div class="detail-body jieda-body">
									<p><?php echo $item['content'];?></p>
								</div>
								<div class="jieda-reply">
									
								<?php if(in_array("{$user['id']}",explode(".",rtrim($item['likes'],'.')))){?>
									<span class="jieda-zan zanok" type="zan">
										<i class="iconfont icon-zan" data-id=<?php echo $item['id'];?> 
											onclick="comment(this,'dzan')"></i>
										<em><?php echo count(explode(".",rtrim($item['likes'],'.')));?></em>
									</span>
								<?php }else {?>
									<span class="jieda-zan zanok" type="zan">
										<i class="iconfont icon-zan" data-id=<?php echo $item['id'];?> 
											style="color: #CCCCCC;"
											onclick="comment(this,'dzan')"></i>
										<em><?php echo count(explode(".",rtrim($item['likes'],'.')));?></em>
									</span>	
								<?php }?>	
									
									<?php if($user && $postinfo['userid'] == $user['id']){?>
										<div class="jieda-admin">
										<span data-id=<?php echo $item['id'];?> 
											onclick="comment(this,'content')" >回复</span>
										<span type="del" data-id=<?php echo $item['id'];?> 
											onclick="comment(this,'delete')" >删除</span>
										<?php if(!$postinfo['finish']){?>
										<span data-id=<?php echo $item['id'];?> onclick="comment(this,'accept')" >采纳</span>
										<?php }?>
									</div>
									<?php }?>
								</div>
							</li>
						<?php }?>						
					<?php }else {?>
						<li class="fly-none">没有任何回答</li>
					<?php }?>
					</ul>
						<?php if($user){?>	
							<div class="layui-form layui-form-pane">

								<form method="post" class="layui-form" >
									<div class="layui-form-item">
										<input disabled type="text" class="toName" id="toName1"  value="" />
										<input type="hidden" name="parentid" id="toName2" value=""   
											autocomplete="off" class="layui-input">
									</div>
										<div class="layui-form-item " >
												<textarea  id="L_content" name="content" required
												lay-verify="required" placeholder="我要回答"
												class="layui-textarea fly-editor" style="height: 150px;"></textarea>
										</div>
									</div>
									<div class="layui-form-item">
										<button class="layui-btn" lay-filter="*" lay-submit>提交回答</button>
									</div>
								</form>
							</div>
						<?php }?>
				</div>
			</div>
		</div>

		<div class="edge">
			<dl class="fly-panel fly-list-one">
				<dt class="fly-panel-title">最近热帖</dt>

				<?php if($hostlist){?>
					<?php foreach($hostlist as $item){?>
						<dd>
							<a href="postinfo.php?postid=<?php echo $item['id'];?>"><?php echo $item['title'];?></a> <span><i
								class="iconfont">&#xe60b;</i> <?php echo $item['cont'];?></span>
						</dd>
					<?php }?>
				<?php }?>
			</dl>
		</div>
</body>
</html>
<script>

//获取帖子id
var postid = <?php echo json_encode($postid);?>;
	
	function comment(obj,action)
	{	
		//获取评论数据
		var comment = <?php echo json_encode($comment);?>;
		
		//获取评论id
		var id = obj.dataset.id?obj.dataset.id:0;
		
		//判断用户是不是点击回复   true 传递id和用户名
		if(action == 'content')
		{
			comment.forEach(function(val,key,self){
				if(val['id']==id)
				{
					document.getElementById("toName1").value = "@"+val['username'];
					document.getElementById("toName2").value = val['id'];
					return false;
				}
			});
		}
		//判断用户是不是点击采纳
		if(action == 'dzan' || action == 'accept' || action == 'delete' || action == 'delete1' || action == 'comment1' || action == 'comment2')
		{	
//			console.log(id);
			window.location.reload();
			Ajax(id,action);
		}

		function Ajax(id,action)
		{
			var ajax = false; //全局变量的作用是用来装ajax对象

			//创建一个ajax对象
			if(window.ActiveXObject)
			{
				//说明是IE浏览器 创建IE浏览器下面的ajax对象
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			}else if(window.XMLHttpRequest)
			{
				ajax = new XMLHttpRequest();
			}

			ajax.onreadystatechange = function()
			{
				//readyState 捕获状态的属性值
				if(ajax.readyState == 4) //数据接收成功
				{
					//判断请求的结果是否成功 请求的结果 http的状态码404,500,400,200
					if(ajax.status == 200) //请求成功
					{
						//获取服务器返回的结果 纯文本类型
						var result = ajax.responseText; 
						console.log(result);

						//把字符串变成对象
//						var obj = JSON.parse(result);
//						console.log(obj);
						return false;
						
//						if(result)
//						{
//							alert("操作成功");
//							location.reload(false);
//							return false;
//						}else{
//							alert("操作失败");
//							return false;
//						}
						// 将对象转换成字符串
						// JSON.stringify()
					}
				}
			}

			//创建一个请求
			ajax.open("post","postinfo.php?postid="+postid,false);

			//如果为post的请求要设置一下HTTP的请求头
			ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

			// 发送请求
			ajax.send(`id=${id}&action=${action}`);
		}
		
		
	}

</script>