<?php




// 对二维数组进行去重复
function more_array_unique($arr)

{

    //先把二维数组中的内层数组的键值记录在在一维数组中

    foreach ($arr[0] as $k => $v) {

        $arr_inner_key[] = $k;

    }

    foreach ($arr as $k => $v) {

        //降维 用implode()也行

        $v = join(",", $v);

        //保留原来的键值 $temp[]即为不保留原来键值

        $temp[$k] = $v;

    }

    //去重：去掉重复的元素

    $temp = array_unique($temp);

    foreach ($temp as $k => $v) {

        //拆分后的重组 如：Array( [0] => 张三 [1] => 18 )

        $a = explode(",", $v);

        //将原来的键与值重新合并

        $arr_after[$k] = array_combine($arr_inner_key, $a);

    }

    return $arr_after;

}


/**

 * 子孙树

 */

function getTree($array,$parentid = 'parentid', $pid =0, $level = 0)
{
	//声明静态数组,避免递归调用时,多次声明导致数组覆盖
	static $list = array();
    
    foreach ($array as $key => $value){
        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
        if ($value[$parentid] == $pid){
            //父节点为根节点的节点,级别为0，也就是第一级
            $value['level'] = $level;
            //把数组放到list中
            $list[] = $value;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($array[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
           getTree($array,$parentid, $value['id'], $level+1);
        }
    }
    return $list;
}

//提醒消息
function showMsg($msg = '',$url="")
{
    //empty 如果为空返回true 否则返回false
    if(empty($url))
    {
        $str = "<script>alert('$msg');history.go(-1);</script>";
    }else{
        $str = "<script>alert('$msg');location.href = '$url';</script>";
    }

    header("Content-Type:text/html;charset=utf-8");
    echo $str;
    exit;
}

//得到当前网址
function get_url(){
	$str = $_SERVER['PHP_SELF'].'?';
	if($_GET){
		foreach ($_GET as $k=>$v){  //$_GET['page']
			if($k!='page'){
				$str .= $k.'='.$v.'&';
			}
		}
	}
	return $str;
}



//分页函数
/**
 *@pargam $current	当前页
 *@pargam $count	记录总数
 *@pargam $limit	每页显示多少条
 *@pargam $size		中间显示多少条
 *@pargam $class	样式
*/
function page($current,$count,$limit,$size,$class='sabrosus'){
	$str='';
	if($count>$limit){
		$pages = ceil($count/$limit);//算出总页数
		$url = get_url();//获取当前页面的URL地址（包含参数）
		
		$str.='<div class="'.$class.'">';
		//开始
		if($current==1){
			$str.='<span class="disabled">首&nbsp;&nbsp;页</span>';
			$str.='<span class="disabled">  &lt;上一页 </span>';
		}else{
			$str.='<a href="'.$url.'page=1">首&nbsp;&nbsp;页 </a>';
			$str.='<a href="'.$url.'page='.($current-1).'">  &lt;上一页 </a>';
		}
		//中间
		//判断得出star与end
	    
		 if($current<=floor($size/2)){ //情况1
			$star=1;
			$end=$pages >$size ? $size : $pages; //看看他两谁小，取谁的
		 }else if($current>=$pages - floor($size/2)){ // 情况2
				 
			$star=$pages-$size+1<=0?1:$pages-$size+1; //避免出现负数
		
			$end=$pages;
		 }else{ //情况3
		 
			$d=floor($size/2);
			$star=$current-$d;
			$end=$current+$d;
		 }
	
		for($i=$star;$i<=$end;$i++){
			if($i==$current){
				$str.='<span class="current">'.$i.'</span>';	
			}else{
				$str.='<a href="'.$url.'page='.$i.'">'.$i.'</a>';
			}
		}
		//最后
		if($pages==$current){
			$str .='<span class="disabled">  下一页&gt; </span>';
			$str.='<span class="disabled">尾&nbsp;&nbsp;页  </span>';
		}else{
			$str.='<a href="'.$url.'page='.($current+1).'">下一页&gt; </a>';
			$str.='<a href="'.$url.'page='.$pages.'">尾&nbsp;&nbsp;页 </a>';
		}
		$str.='</div>';
	}
	
	return $str;
}


/**
 * 获得随机字符串
 * @param $len             需要的长度
 * @param $special        是否需要特殊符号
 * @return string       返回随机字符串
 */
function getRandomStr($len = 10, $special=false){
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );

    if($special){
        $chars = array_merge($chars, array(
            "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
            "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
            "}", "<", ">", "~", "+", "=", ",", "."
        ));
    }

    $charsLen = count($chars) - 1;
    shuffle($chars);                            //打乱数组顺序
    $str = '';
    for($i=0; $i<$len; $i++){
        $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
    }
    return $str;
}


/**
 *	实现文件的上传
 *  @param string 上传的表单中类型为file的name值
 *  @param int 	  上传文件大小的限制
 *  @param array  上传文件类型的限制
 *  @param string 上传文件保存的目录
 *  @return string 错误，返回对应错误信息，正确，返回图片信息。
 */	
function uploads($name='img',$path='uploads',$size=1048576,$arr=array('jpg','png','gif')){
	$num= $_FILES[$name]['error'];
	if($num>0){
		if($num==1){
			echo '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。';
			exit;
		}else if($num==2){
			echo '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。';
			exit;
		}else if($num==3){
			echo '文件只有部分被上传。 ';
			exit;
		}else if($num==4){
			echo '没有文件被上传';
			exit;
		}else{
			echo '其他情况';
			exit;
		}		
	}
	// 再次拦截
	 if($_FILES[$name]['size']>$size){
		echo  '你是故意的';
		exit;
	 }
	 
	  //文件后缀名
	 $pre=pathinfo($_FILES[$name]['name'],PATHINFO_EXTENSION);


	
	  if(!in_array($pre,$arr)){
		echo '文件的类型不对';
		exit;
	  }
	//文件名跟文件后缀需要重新处理
	$filename=date('Ymdhis').mt_rand(1000,9999).'.'.$pre;
	
	  //判断是否是通过http post上传过来的文件
	 if(is_uploaded_file($_FILES[$name]['tmp_name'])){
		move_uploaded_file($_FILES[$name]['tmp_name'],$path.'/'.$filename);
		return $filename;
	  }else{
		return false;
	  }
}


?>