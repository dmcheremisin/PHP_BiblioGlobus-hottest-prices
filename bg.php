<?php
require_once('connect.php');
$now = strtotime("now");
$created = filemtime('bghot.xml');
$dif = ($now - $created)/3600;
if($dif > 3){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,'https://login.bgoperator.ru/auth');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	$data = array('login' => LOGIN, 'pwd' => PASSWORD);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	$out = curl_exec($curl);
	//var_dump($out);
	curl_close($curl);

	//echo "<hr/>";
	$cookie = explode(" ",$out);
	$Z=$cookie[14];
	$Z=str_replace('Z=','', $Z);
	$Z=str_replace(';','', $Z);
	//echo $Z;
	//echo "<hr/>";
	$A=$cookie[18];
	$A=str_replace('A=','', $A);
	$A=str_replace(';','', $A);
	//echo $A;
	//echo "<hr/>";
	$L=$cookie[22];
	$L=str_replace('L=','', $L);
	$L=str_replace(';','', $L);
	//echo $L;
	//echo "<hr/>";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://export.bgoperator.ru/yandex?action=minprice&cityId=100510001075');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_COOKIE, "Z=".$Z.";A=".$A.";L=".$L);
	$out = curl_exec($curl);
	file_put_contents('bghot.xml',$out);
	curl_close($curl);
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://export.bgoperator.ru/auto/auto-kurs.xml');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_COOKIE, "Z=".$Z.";A=".$A.";L=".$L);
	$out = curl_exec($curl);
	file_put_contents('bgcourse.xml',$out);
	curl_close($curl);
}
$course = simplexml_load_file('bgcourse.xml');
$eur = (string) $course -> rate[0]['value'];
$eur=str_replace('.', ',', $eur);
$usd = (string) $course -> rate[1]['value'];
$usd = str_replace('.', ',', $usd);
$today = new DateTime("now");
?>
<table style="width: 1024px; border-collapse: collapse; border: 1pt solid;">
<tr>
	<td>Страна</td>
	<td>Отправление</td>
	<td>Ночей</td>
	<td>Цена/чел</td>
</tr>
<?php
$file = simplexml_load_file('bghot.xml');
foreach($file -> city -> entry as $entry){
	
	$date = new DateTime($entry -> date);
	$dif = (strtotime($date -> format('Y-m-d')) - strtotime($today -> format('Y-m-d')))/3600/24;
	
	if( $dif > 1 ){
		switch($entry -> cost -> valute){
			case 'EUR':
				$mult = $eur;
				break;
			case 'USD':
				$mult = $usd;
				break;
			case 'RUB':
				$mult = 1;
				break;
		}
		$price = round(($entry -> cost -> amount)*$mult*1.02);
		if($price < 80000){
			echo "<tr>";
			echo "<td>".$entry -> country -> name."</td>";
			echo "<td>".$entry -> date."</td>";
			echo "<td>".$entry -> duration."</td>";
			echo "<td>".$price." руб. </td>";
			echo "</tr>";
		}
	}
}
?>
</table>