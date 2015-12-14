<div id='bggarant'>
<script>
function showbggarant(str){
	var XMLHttpgarant=new window.XMLHttpRequest();
	XMLHttpgarant.onreadystatechange = function(){
		if(XMLHttpgarant.readyState == 4){
			document.getElementById("bggarant").innerHTML = XMLHttpgarant.responseText;
		}
	}
	XMLHttpgarant.open("GET", "http://avit-spb.ru/bg/bggarant.php?country="+str,true);
	XMLHttpgarant.send();
}
</script>
<h2 style='color:#1E73BE;' align="center">Спецпредложения с мгновенным подтверждением</h2>
<?php
require_once('functions.php');
$country = cleanvalue($_GET['country']);
$today = new DateTime("now");
$now = strtotime("now");
$created = filemtime('bggarant.txt');
$dif = ($now - $created)/3600;
if($dif > 3){
	getfile('http://export.bgoperator.ru/auto/auto-spo-100510001075v2-124331253701.js','bggarant.txt');
}

$created = filemtime('bgcourse.xml');
$dif = ($now - $created)/3600;
if($dif > 3){
	getfile('http://export.bgoperator.ru/auto/auto-kurs.xml','bgcourse.xml');
}

$course = simplexml_load_file('bgcourse.xml');
$eur = (string) $course -> rate[0]['value'];
$eur=str_replace('.', ',', $eur);
$usd = (string) $course -> rate[1]['value'];
$usd = str_replace('.', ',', $usd);
ob_start();
	?>
	<table>
	<tr>
		<td width="15%" style='color:#1E73BE; align:center;' align="center">Отправление</td>
		<td width="15%" style='color:#1E73BE; align:center;' align="center">Страна</td>
		<td width="32%" style='color:#1E73BE; align:center;' align="center">Отель</td>
		<td width="13%" style='color:#1E73BE; align:center;' align="center">Ночей</td>
		<td width="10%" style='color:#1E73BE; align:center;' align="center">Питание</td>
		<td width="15%" style='color:#1E73BE; align:center;' align="center">Цена от</td>
	</tr>
	<?php
	$file = file_get_contents('bggarant.txt');
	//var_dump($file);
	$prices = json_decode($file, true);
	foreach($prices as $key => $row){
		$price[$key] = $row['RUR'];
	}
	array_multisort($price, SORT_ASC, SORT_NUMERIC, $prices);
	//echo "<pre>";
	//print_r($prices);
	//echo "</pre>";
	foreach($prices as $row){
		//echo $row['data']."<br/>";
		
		$date = new DateTime($row['data']);
		$dif = (strtotime($date -> format('Y-m-d')) - strtotime($today -> format('Y-m-d')))/3600/24;
		
		if( $dif > 1 ){
			switch($row['val']){
				case 'EUR':
					$mult = $eur;
					break;
				case 'USD':
					$mult = $usd;
					break;
				case 'RUR':
					$mult = 1;
					break;
			}
			$price = round(($row['amount'])*$mult*1.02);
			if($prev != $row['cntryName'] and $price < 50000) $countries[] = $row['cntryName'];
					else $prev = $row['cntryName'];
			if( (!empty($country) and $row['cntryName'] == $country and $price < 50000) or  (empty($country) and $price < 50000)){
				echo "<tr>";
				echo "<td style='color:#1E73BE;'>".$row['data']."</td>";
				echo "<td style='color:#1E73BE;'>".$row['cntryName']."</td>";
				echo "<td style='color:#1E73BE;'>".$row['hotelName']."</td>";
				echo "<td style='color:#1E73BE;'>".$row['dur']." </td>";
				echo "<td style='color:#1E73BE;'>".$row['meal']." </td>";
				echo "<td style='color:#1E73BE;'>".$price." руб. </td>";
				echo "</tr>";
			}
		}
	}
	?>
	</table>
	<?php
	$table = ob_get_contents();
ob_end_clean();

$ucountries = array_unique($countries, SORT_STRING);
echo "<p align='center'>";
echo "<span style='color:#388CBC; cursor:pointer;' onClick=\"showbggarant('');\"> &rarr;Все страны</span>&nbsp;&nbsp;&nbsp;";
foreach($ucountries as $country){
	echo "<span style='color:#388CBC; cursor:pointer;' onClick=\"showbggarant('$country');\"> &rarr;".$country."</span>&nbsp;&nbsp;&nbsp;";
}
echo "</p>";
echo $table;
?>
</div>