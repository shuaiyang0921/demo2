// 删除函数
function cut(obj){
	// 获取自定义属性的值 规则id
	var ruleid = obj.dataset.ruleid;
	// 把规则id赋值给隐藏域value
	var cut = $("input[name='delete[]']").val(ruleid);
}


// 批量删除
// 获取所有复选框
var checkbox = $("input[name='delete[]']");
// 复选框的个数
var length = checkbox.length;
// 给表格添加改变事件 复选框没有被选中,批量删除无法点击
$("tbody").on('change',function(){
	// 循环判断复选框是否被选中
	for(var i = 0;i < length; i++){
		// 选中就给批量删除点击,并且结束她(避免被循环覆盖)
		if(checkbox[i].checked){
			$("td[class='multiple']").html("<a class='btn btn-primary' href='#myModal' onclick='batch()' data-toggle='modal'>批量删除</a>");
			return false;
		}else{
			// 没有选中不给点击
			$("td[class='multiple']").html("<a class='btn' disabled href='javascript:void(0)'>批量删除</a>");
		}
	}
})

// 给批量删除按钮添加点击事件，把id值传给后台
function batch(){
	// 定义一个空数组
	var ruleid = [];
	// 循环复选框
	for(var i = 0;i < length; i++){
		// 判断复选框状态，选中就将其id添加到数组
		if(checkbox[i].checked){
			ruleid.push(checkbox[i].value);
		}
	}
	// 把数组转换成字符串，用 , 分隔
	ruleid = ruleid.join(',');
	// 把得到的字符串赋值给隐藏域的 value
	$("input[name='delete[]']").val(ruleid);
}

// ajax函数
function getAjsx(did,url,fun){
	$.ajax({
		url:url,
		type:"get",
		dataType:"json",
		data:did,
		success:function(data){
			// 调用回调函数
			fun(data);
		},
		error:function(error){
			console.log(error);
		}
	});
}
