<?php

	header('Content-Type: text/html; charset=UTF-8');
	require_once './mysql.class.php';
	require_once './response.class.php';


	$rawPostData = file_get_contents("php://input",'r');
	$postData = json_decode($rawPostData,true);

	// 判断数据正确性
	if(empty($postData['where']) || empty($postData['price'])){
		// 返回数据
		$re = new Response();
		$error = '1';
		$message = '暂时无法提供数据，请联系小程序管理员';
		$re->show(200,$error,$message);
	}

	// 查找符合的条件
	$sqlhelper = new sqlhelper();
	$sql = "select * from canteen where location = '{$postData['where']}' and price >= {$postData['price']}";
	$dataSQL = $sqlhelper->fetchall($sql);
	$sqlhelper->close_connect();
	
	// 随机食物
	$randNum = array_rand($dataSQL,1); 
	$data = $dataSQL[$randNum];
	
	// 爬虫相关图片
	$url_img = "http://home.meishichina.com/search/{$data['name']}";

	$html_img = http_get($url_img); // 提交请求
	$html_img = deal_str($html_img); // 格式化文字
	
	$partten = '/<img class="imgLoad" src=".*?" data-src="(.*?)"/';
	preg_match($partten, $html_img, $parttenData);
	$data['img'] = $parttenData[1]; 

	// 返回信息
	$re = new Response();
	$re->show(200,0,$data);


	/**
	 * [转换字符集，去除空白标签]
	 * @param  [type] $html_str [处理前数据]
	 * @return [type]           [处理后数据]
	 */
	function deal_str($html_str){
		// 处理字符集
		$coding = mb_detect_encoding($html_str);
		if ($coding != "UTF-8" || !mb_check_encoding($html_str, "UTF-8"))
			$html_str = mb_convert_encoding($html_str, 'utf-8', 'GBK,UTF-8,ASCII');

		// 去除空标签
		$html_str = preg_replace("/[\t\n\r]+/", "", $html_str);

		return $html_str; // 返回数据
	}

	/**
	 * http get请求
	 * @param string $url    传入的网址
	 * @return string $data  返回网页数据
	 */
	function http_get($url){
	    $curl = curl_init();
	    curl_setopt($curl,CURLOPT_URL,$url);
	    curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0"); 
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1); 
	    $data = curl_exec($curl);
	    if(curl_errno($curl)){
	        return 'ERROR'.curl_errno($curl);
	    }
	    curl_close($curl);
	    return $data;
	}


/**
 * id name price where 
 */
