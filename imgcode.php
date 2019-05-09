<?php

//处理随机字符函数
function get_rand_str($length = 4){
    $chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $str = str_shuffle($chars); // 随机打乱一个字符串
    $str = substr($str,0,$length);
    $str = strtolower($str);
    return $str;
  }
  
  $width = 45;   //缩略图宽度
  $height = 18;  //缩略图高度
  
  //新建一个真彩色图像
  $img = imagecreatetruecolor($width,$height);
  
  //背景颜色
  $backgroundcolor = imagecolorallocate($img,74,147,223); 
  
  //文字颜色
  $textcolor = imagecolorallocate($img,255,255,255);   
  
  //画一矩形并填充
  imagefilledrectangle($img,0,0,$width,$height,$backgroundcolor);
  
  //获取随机数
  $get_code = get_rand_str();
  
  //水平地画一行字符串
  imagestring($img,5,6,1,$get_code,$textcolor); 
  
  //在图片当中去画一些点 防止有人而已破解验证码
  for($i=0;$i<=20;$i++){
    $x = mt_rand(0,$width);
    $y = mt_rand(0,$height);
    imagesetpixel($img,$x,$y,imagecolorallocate($img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255)));
  }
  
  //session 是存放在服务器上面的缓存 在客户端上面是找不到的
  session_start();  //开启session会话控制将验证码缓存
  $_SESSION['imgcode'] = $get_code;  //把生成的随机数放到session里面的一个变量当中
  
  header("Content-Type:image/png");
  imagepng($img);   //在浏览器上面输出一张图片
  imagedestroy($img);  //销毁图片


?>