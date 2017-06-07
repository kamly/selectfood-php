<?php

class Response {
	const JSON = "json";
	/**
	* 按综合方式输出通信数据
	* @param integer $code 状态码
	* @param string $error 提示信息
	* @param array $data 数据
	* @param string $type 数据类型
	* return string
	*/
	public static function show($code, $error= '', $data = array(), $type = self::JSON) {
		if(!is_numeric($code)) {
			return '';
		}

		$type = isset($_GET['format']) ? $_GET['format'] : self::JSON;

		$result = array(
			'code' => $code,
			'error' => $error,
			'data' => $data,
		);

		if($type == 'json') {
			self::json($code, $error, $data);
			exit;
		} elseif($type == 'array') {
			var_dump($result);
		} elseif($type == 'xml') {
			self::xmlEncode($code, $error, $data);
			exit;
		} else {
			// TODO
		}
	}
	/**
	* 按json方式输出通信数据
	* @param integer $code 状态码
	* @param string $error 提示信息
	* @param array $data 数据
	* return string
	*/
	public static function json($code, $error = '', $data = array()) {
		
		if(!is_numeric($code)) {
			return '';
		}

		$result[] = array(    //这里要加[]不然前端接受不了
			'code' => $code,
			'error' => $error,
			'data' => $data
		);

		echo json_encode($result);
		exit;
	}

	/**
	* 按xml方式输出通信数据
	* @param integer $code 状态码
	* @param string $error 提示信息
	* @param array $data 数据
	* return string
	*/
	public static function xmlEncode($code, $error, $data = array()) {
		if(!is_numeric($code)) {
			return '';
		}

		$result = array(
			'code' => $code,
			'error' => $error,
			'data' => $data,
		);

		header("Content-Type:text/xml");
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml .= "<root>\n";

		$xml .= self::xmlToEncode($result);

		$xml .= "</root>";
		echo $xml;
	}

	public static function xmlToEncode($data) {

		$xml = $attr = "";
		foreach($data as $key => $value) {
			if(is_numeric($key)) {
				$attr = " id='{$key}'";
				$key = "item";
			}
			$xml .= "<{$key}{$attr}>";
			$xml .= is_array($value) ? self::xmlToEncode($value) : $value;
			$xml .= "</{$key}>\n";
		}
		return $xml;
	}

}
