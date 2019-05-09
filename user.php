<?php
include_once("./includes/init.php");

//检测用户是否登录 如果没有登录就跳转
$user = checkUser();

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
      <!--
      <div class="fly-msg" style="margin-top: 15px;">
        您的邮箱尚未验证，这比较影响您的帐号安全，<a href="activate.html">立即去激活？</a>
      </div>
      -->
      <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
          <li data-type="mine-jie" lay-id="index" class="layui-this">我发的帖（<span>89</span>）</li>
          <li data-type="collection" data-url="/collection/find/" lay-id="collection">我收藏的帖（<span>16</span>）</li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
          <div class="layui-tab-item layui-show">
            <ul class="mine-view jie-row">
              <li>
                <a class="jie-title" href="../jie/detail.html" target="_blank">基于 layui 的极简社区页面模版</a>
                <i>2017/3/14 上午8:30:00</i>
                <a class="mine-edit" href="/jie/edit/8116">编辑</a>
                <em>661阅/10答</em>
              </li>
              <li>
                <a class="jie-title" href="../jie/detail.html" target="_blank">基于 layui 的极简社区页面模版</a>
                <i>2017/3/14 上午8:30:00</i>
                <a class="mine-edit" href="/jie/edit/8116">编辑</a>
                <em>661阅/10答</em>
              </li>
              <li>
                <a class="jie-title" href="../jie/detail.html" target="_blank">基于 layui 的极简社区页面模版</a>
                <i>2017/3/14 上午8:30:00</i>
                <a class="mine-edit" href="/jie/edit/8116">编辑</a>
                <em>661阅/10答</em>
              </li>
            </ul>
            <div id="LAY_page"></div>
          </div>
          <div class="layui-tab-item">
            <ul class="mine-view jie-row">
              <li>
                <a class="jie-title" href="../jie/detail.html" target="_blank">基于 layui 的极简社区页面模版</a>
                <i>收藏于23小时前</i>  </li>
            </ul>
            <div id="LAY_page1"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>