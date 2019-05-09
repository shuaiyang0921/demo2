<?php 
    include_once('../includes/init.php');

    $admin = checkAdmin();

    $config = array(
        "system"=>PHP_OS,
        "version"=>PHP_VERSION,
        "addr"=>$_SERVER['SERVER_ADDR'],
        "host"=>$_SERVER['SERVER_NAME'],
        "software"=>$_SERVER['SERVER_SOFTWARE'],
        "mysql"=>mysql_get_client_info()
    );
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once('meta.php'); ?>
  </head>

  <body>     
    <?php 
        include_once('header.php');
        include_once('menu.php');
    ?>
    <div class="content">
        <div class="header">
            <h1 class="page-title">后台首页</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="homepage.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <ul style="font-size:16px;">
                <li>操作系统：<?php echo $config['system']; ?></li>
                <li>PHP版本：<?php echo $config['version']; ?></li>
                <li>IP地址：<?php echo $config['addr']; ?></li>
                <li>主机名：<?php echo $config['host']; ?></li>
                <li>服务器：<?php echo $config['software']; ?></li>
                <li>数据库：<?php echo $config['mysql']; ?></li>
            </ul>
            <div class="row-fluid">
                <footer>
                    <hr>
                    <p>&copy; 2017 <a href="#" target="_blank">copyright</a></p>
                </footer>
            </div>
        </div>
    </div>
  </body>
</html>
