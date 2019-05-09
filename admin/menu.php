<?php 
	include_once('../includes/init.php'); 
	$admin = checkAdmin();
	// 判断是否有权限
  	checkAuth();

	// 查找管理员权限角色
	$sql = "SELECT auth.*,admin.groupid FROM {$pre_}auth_group AS auth LEFT JOIN {$pre_}admin AS admin ON auth.id = admin.groupid WHERE admin.id = ".$admin['id'];
	$group = $db->find($sql);

	// 查找管理员角色下面的权限规则
	$ruleid = $group['rules'];
	$sql = "SELECT * FROM {$pre_}auth_rule WHERE id IN ($ruleid) AND ismenu = 1 ORDER BY id ASC";
	$menuRulesList = $db->select($sql);


	/*// 调用递归函数 无限级导航栏
	$menuRulesList = getTree($rules,'pid');
	// 调用二维数组去重函数
	$menuRulesList = more_array_unique($menuRulesList);*/
?>
<div class="sidebar-nav">
    <a href="#dashboard-menu" class="nav-header" data-toggle="collapse"><i class="icon-dashboard"></i>控制面板</a>
    <ul id="dashboard-menu" class="nav nav-list collapse in">
    	<?php foreach($menuRulesList as $item){ ?>
    		<!-- <?php if($item['ismenu']){ ?> -->
        		<li><a href="<?php echo $item['name']; ?>"><?php echo $item['title']; ?></a></li>
        	<!-- <?php } ?> -->
 		<?php } ?>
    </ul>
</div>
<script src="../assets/admin/lib/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript">
    $("[rel=tooltip]").tooltip();
    $(function() {
        $('.demo-cancel-click').click(function(){return false;});
    });
</script>