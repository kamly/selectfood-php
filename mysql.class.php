<?php
error_reporting(E_ALL ^ E_DEPRECATED);
//此类完成对数据库的操作  打印出数据的格式，在慢慢调试。
class sqlhelper{
	private $conn;
	private $dbname="";//数据库名
	private $username="";//数据库账号
	private $password="";//数据库密码
	private $host="";//主机名
	private $port="";

	//连接数据库
	//这是一个构造方法，若果创建了new sqlhelper之后即可自动调用
    public function __construct(){  	
		 $this->conn=mysql_connect($this->host.':'.$this->port,$this->username,$this->password);
		 if(!$this->conn){
		 	die("连接数据库失败".mysql_errno());
		 }
       mysql_query("set names utf8",$this->conn) or die(mysql_errno());
       mysql_select_db($this->dbname,$this->conn) or die(mysql_errno());      
	}


	//插入功能
	//$table表名字，$array数据表中的元素，$newarray插入的值
	//第一次数组设置错误，应该是'stusername'=>$_POST['stusername'] 这样就不需要设置2个数组
	function insert($table,$array){
		$keys=join(",",array_keys($array));
		$vals="'".join("','",array_values($array))."'";
		$sql = "INSERT INTO {$table} ({$keys}) VALUES ({$vals})";
		//var_dump($keys);
		//var_dump($vals);
		//echo $sql;
        mysql_query($sql);
        //var_dump(mysql_insert_id()); //返回插入的行数,要设置主键且排序才返回
        //return mysql_insert_id();
        return mysql_affected_rows();	
	}

	//更新功能
	//不是根据序号查找信息，应该  $where ="User_NickName = '".$nikename."'"; 缺少''
	function update($table,$array,$where=null){
		$str=null;
		foreach ($array as $key=>$val){
			if($str==null){
				$sep="";
			}else{
				$sep=",";
			}
			$str.=$sep.$key."='".$val."'";			
		}
			// var_dump($str);
			// var_dump($where);
			$sql="update {$table} set {$str}".($where==null?null:" where ".$where.";");
			//echo $sql;
			mysql_query($sql);
			// var_dump(mysql_affected_rows()); //返回影响的行数
	        return mysql_affected_rows();
	}

	//删除
	function delete($table,$where=null){
		$where=$where==null?null:"where ".$where;
		//var_dump($where);  
		$sql="delete from {$table} {$where}".";";
		//print_r($sql);//exit();
		mysql_query($sql);
		return mysql_affected_rows();	
	}


	//查找一条信息
	function fetchone($sql,$result_type=MYSQL_ASSOC){
		//MYSQL_ASSOC只得到关联索引
		//print_r($sql);exit();
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result,$result_type);
		//var_dump($row);  //直接 echo $row['stid'] 就出现里面的数据
		return $row;
	}

	//取出所有数据
	function fetchall($sql,$result_type=MYSQL_ASSOC){
		$result=mysql_query($sql);
		while($row=mysql_fetch_array($result,$result_type)){
			$rows[]=$row;
		}
		//var_dump($rows);
		//var_dump($rows[0]['name']);
		return $rows;
	}

	//得到结果集中的条数
	function getResultNum($sql){
		// var_dump($sql);
		$result=mysql_query($sql);
		//echo mysql_num_rows($result);	
		return mysql_num_rows($result);	
	}


	//关闭数据库连接
	public function close_connect(){
		if(!empty($this->conn)){
			mysql_close($this->conn);
		}
	}

}
?>
