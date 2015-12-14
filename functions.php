<?php
require_once('connect.php');

function cleanvalue($value){
	$value = trim($value);
    $value = strip_tags($value);
    $value = addslashes($value);
	return $value;
}

function getfile($url,$filename, $return = false){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,'https://login.bgoperator.ru/auth');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	$data = array('login' => LOGIN, 'pwd' => PASSWORD);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	$out = curl_exec($curl);
	curl_close($curl);

	$cookie = explode(" ",$out);
	$Z=$cookie[14];
	$Z=str_replace('Z=','', $Z);
	$Z=str_replace(';','', $Z);

	$A=$cookie[18];
	$A=str_replace('A=','', $A);
	$A=str_replace(';','', $A);

	$L=$cookie[22];
	$L=str_replace('L=','', $L);
	$L=str_replace(';','', $L);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_COOKIE, "Z=".$Z.";A=".$A.";L=".$L);
	$out = curl_exec($curl);
	if($return == false){
		file_put_contents($filename,$out);
		curl_close($curl);
	} else {
		curl_close($curl);	
		return $out;
	}
}
?>