<?php  
    include_once('../includes/init.php');

    $action = isset($_GET['action']) ? $_GET['action'] : false;
    if($action == 'ruleadd'){
        $sql = "SELECT * FROM {$pre_}auth_rule WHERE id = ".$_GET['ruleid'];
        $ruleStr = $db->find($sql);
        if(!$ruleStr){
            showMsg('规则不存在','rulelist.php');
            exit;
        }
        // var_dump($ruleStr);
    }
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <?php include_once('meta.php'); ?>
</head>

<body>
    <!-- <?php include_once('header.php'); ?>
    <?php include_once('menu.php'); ?> -->
    <div class="content">
        <!-- <div class="header">
            <h1 class="page-title">规则编辑</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">规则编辑</li>
        </ul> -->
        <div class="container-fluid">
            <div class="row-fluid">
                <!-- <div class="btn-toolbar">
                    <button class="btn btn-primary" onClick="location='rulelist.php'"><i class="icon-list"></i> 规则列表</button>
                  <div class="btn-group">
                  </div>
                </div> -->
                <div class="well">
                    <div id="myTabContent" class="tab-content">
                      <div class="tab-pane active in" id="home">
                        <form method="post">
                            <label>规则归属</label>
                            <input type="number" name="pid" required value="<?php echo $ruleStr['pid']; ?>" class="input-xxlarge">
                            <label>规则地址</label>
                            <input type="text" name="name" required value="<?php echo $ruleStr['name']; ?>" class="input-xxlarge">
                            <label>规则标题</label>
                            <input type="text" name="title" required value="<?php echo $ruleStr['title']; ?>" class="input-xxlarge">
                            <label>规则状态</label>
                            <select name="status" class="input-xlarge">
                            <?php if($ruleStr['status'] == 1){ ?>
                                <option value="1" selected >启用</option>
                                <option value="0">禁用</option>
                            <?php }else{ ?>
                                <option value="1">启用</option>
                                <option value="0" selected >禁用</option>
                            <?php } ?>
                            </select>
                            <label>是否显示</label>
                            <select name="ismenu" class="input-xlarge">
                            <?php if($ruleStr['ismenu'] == 1){ ?>
                                <option value="1" selected >显示</option>
                                <option value="0">隐藏</option>
                            <?php }else{ ?>
                                <option value="1">显示</option>
                                <option value="0" selected >隐藏</option>
                            <?php } ?>
                            </select>
                            <label></label>
                            <input class="btn btn-primary" type="submit" value="提交" />
                        </form>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(".content").css({"margin-left":"0px","background":"#000"},{"header":"500px"},{"margin":"0"},{"padding":"0"});
        $("body").css({"margin-left":"0px","background":"#000"},{"header":"500px"},{"margin":"0"},{"padding":"0"});
        function iframeFunc() {
            alert("我是iframe.html 页面的 iframeFunc 方法");
        }
    </script>
</body>

</html>