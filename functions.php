<?php
class Functions{
	
	const LOGIN = 'your login';
	const PASSWORD = 'your password';
	private static $Z, $A, $L;
	
	public static function cleanvalue($value){
		$value = trim($value);
		$value = strip_tags($value);
		$value = addslashes($value);
		return $value;
	}
	
	private static function getCookies(){
		$data = array('login' => self::LOGIN, 'pwd' => self::PASSWORD);
		$auth = http_build_query($data);
		$opts = [ 	'method' => 'POST',
					'header' => "Content-type: application/x-www-form-urlencoded\r\n".
						"Accept-language: ru\r\n".
						"Content-Length: " . strlen($auth) . "\r\n",
						'content' => $auth
		];
		$contextOptions=[ "ssl"=> [ 	"verify_peer"=>false, 
										"verify_peer_name"=>false] ];  
		$context = stream_context_create($contextOptions);
		stream_context_set_option($context, ['http' => $opts] );

		$data = fopen('https://login.bgoperator.ru/auth', 'r', false, $context);
		$meta = stream_get_meta_data($data);
		self::$Z = substr($meta['wrapper_data'][6], 14, 128)."<br>";
		self::$A =  substr($meta['wrapper_data'][7], 14, 133)."<br>";
		self::$L =  substr($meta['wrapper_data'][8], 14, 6)."<br>";	
		fclose($data);
	}
	
	public static function getFile($url, $file, $return = false){
		self::getCookies();
		
		echo self::$Z.self::$A.self::$L;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip'); 
		curl_setopt($curl, CURLOPT_COOKIE, "Z=".self::$Z.";A=".self::$A.";L=".self::$L);
		$out = curl_exec($curl);
		if($return == false){
			file_put_contents($file, $out);
			curl_close($curl);
		} else {
			curl_close($curl);
			echo $out;
		}
	}
}