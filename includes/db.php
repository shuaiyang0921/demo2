<?php
//封装一个类

//有兴趣可以自己封装一个升级版
// $db->field("*")->table("job")->where()->orderby("id,desc")->select();


class DB
{
    public $conn;
    public $host;
    public $username;
    public $password;
    public $dbname;
    public $prefix = "pre_";
    public $charset = "utf8";

    //接收参数
    function __construct($host = "localhost",$username ="root",$password ="root",$dbname = null)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        //调用连接数据库方法
        if($this->connect())
        {
            //选择数据库
            $this->selectDB();

            //设置编码
            $this->charSet();
        }
    }



    //连接数据库
    function connect()
    {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password);

        if(!$this->conn) //失败会走进去
        {
            echo "连接数据库失败";
            exit;
        }else{
            return $this->conn;
        }
        
    }

    //选择数据库
    function selectDB()
    {
        return mysqli_select_db($this->conn,$this->dbname);
    }

    //设置编码
    function charSet()
    {
        $res = mysqli_query($this->conn,"SET NAMES $this->charset");

        if(!$res)
        {
            //如果有报错就走到里面来，错误日志方法
            $this->errorLog();
        }

        return $res;
    }

    //查询单条
    function find($sql)
    {
        $res = mysqli_query($this->conn,$sql);

        if(!$res)
        {
            $this->errorLog();
        }

        return mysqli_fetch_assoc($res);
    }
    

    //查询多条
    function select($sql)
    {
        $res = mysqli_query($this->conn,$sql);

        if(!$res)
        {
            $this->errorLog();
        }

        //从资源结果对象中获取数据
        $data = array();
        while($row = mysqli_fetch_assoc($res))
        {
            $data[] = $row;
        }

        //查询多条用二维数组 查询单条一维数组
        return $data;
    }

    //插入
    function add($data,$table)
    {
        $table = $this->prefix.$table;

        //INSERT INTO $table(`name`,`depaid`,`depaid`)VALUES()

        //获取数组中的索引
        $keys = array_keys($data);
        $keyStr = "`".implode("`,`",$keys)."`";
        $valueStr = "'".implode("','",$data)."'";
        $sql = "INSERT INTO $table($keyStr)VALUES($valueStr)";

        $res = mysqli_query($this->conn,$sql);

        if(!$res)
        {
            $this->errorLog();
        }

        //返回插入的影响行数
        return mysqli_insert_id($this->conn);
    }

    //更新
    function update($data,$table,$where = 1)
    {
        $str = "";
        foreach($data as $key=>$item)
        {
            $str .= "$key='$item',";
        }

        //去掉,
        $str = trim($str,",");

        $table = $this->prefix.$table;

        $sql = "UPDATE $table SET $str WHERE $where";
//      echo $sql;exit;
        $res = mysqli_query($this->conn,$sql);

        if(!$res)
        {
            $this->errorLog();
        }

        //返回影响行数
        return mysqli_affected_rows($this->conn);
    }

    //删除
    function delete($table,$where = 1)
    {
        $table = $this->prefix.$table;
        $sql = "DELETE FROM $table WHERE $where";
//		var_dump($sql);exit;
        $res = mysqli_query($this->conn,$sql);

        if(!$res)
        {
            $this->errorLog();
        }

        //返回影响行数
        return mysqli_affected_rows($this->conn);
    }


    //执行源生sql语句的操作方法
    function runSql($sql)
    {
        $res = mysqli_query($this->conn,$sql);

        if(!$res)
        {
            $this->errorLog();
        }

        //返回影响行数
        return $res;
    }

    //错误日志方法
    function errorLog()
    {
        $logfile = str_replace("\\","/",dirname(dirname(__FILE__))."/assets/logs/mysqlLog.txt");
        
        $log = mysqli_error($this->conn);
        $date = date("Y-m-d H:i:s",time());
        $msg = "$date\r\n错误信息：$log\r\n\r\n";
        // 2019-03-13 12:12:12
        // 错误信息：$log

        // 2019-03-13 12:12:12
        // 错误信息：$log
        file_put_contents($logfile,$msg,FILE_APPEND);

        //弄一个错误页面显示，先跳转然后在显示
        echo "sql语句执行失败，请查看错误日志";
        exit();
    }
}

?>