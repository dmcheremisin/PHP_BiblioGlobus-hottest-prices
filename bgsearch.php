<?php
//112@bgoperator.com
require_once('functions.php');
	$serchdate=date("d.m.Y",strtotime($_POST['datetime']));
	$resort = cleanvalue($_POST['resort']);
	$nights = cleanvalue($_POST['nights']);
	$adults = cleanvalue($_POST['adults']);
	$children = cleanvalue($_POST['children']);
	$hotel = cleanvalue($_POST['hotel']);
	$meal = cleanvalue($_POST['meal']);	
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<p>Дата: <input type="date" name="datetime" value="<?=((!empty($serchdate)) ? "$serchdate":"")?>" ></p>
<p>Курорт: <select name="resort">
	<option value="100510001067" <?=(($resort==100510001067) ? "selected":"")?> >Айя-Напа</option>
	<option value="100510001068" <?=(($resort==100510001068) ? "selected":"")?> >Ларнака</option>
	<option value="100510001069" <?=(($resort==100510001069) ? "selected":"")?> >Лимассол</option>
	<option value="100510001070" <?=(($resort==100510001070) ? "selected":"")?> >Протарас</option>
	<option value="100510001071" <?=(($resort==100510001071) ? "selected":"")?> >Пафос</option>
</select>
<p>Ночей: <input type="text" name="nights" value="<?=((!empty($nights)) ? "$nights":"")?>"></p>
<p>Отель: <select name="hotel">
	<option value="1*" <?=(($hotel=='1*') ? "selected":"")?> >1*</option>
	<option value="2*" <?=(($hotel=='2*') ? "selected":"")?> >2*</option>
	<option value="3*" <?=(($hotel=='3*') ? "selected":"")?> >3*</option>
	<option value="4*" <?=(($hotel=='4*') ? "selected":"")?> >4*</option>
	<option value="5*" <?=(($hotel=='5*') ? "selected":"")?> >5*</option>
</select>
</p>
<p>Питание: <select name="meal">
	<option value="AO" <?=(($meal=='AO') ? "selected":"")?> >AO</option>
	<option value="BB" <?=(($meal=='BB') ? "selected":"")?> >BB</option>
	<option value="HB" <?=(($meal=='HB') ? "selected":"")?> >HB</option>
	<option value="FB" <?=(($meal=='FB') ? "selected":"")?> >FB</option>
	<option value="AI" <?=(($meal=='AI') ? "selected":"")?> >AI</option>
</select>
</p>
<p>Взрослых: <select name="adults">
	<option value="1" <?=(($adults=='1') ? "selected":"")?> >1</option>
	<option value="2" <?=(($adults=='2') ? "selected":"")?> >2</option>
	<option value="3" <?=(($adults=='3') ? "selected":"")?> >3</option>
</select>
</p>
<p>Детей: <select name="children">
	<option value="0" <?=(($children=='0') ? "selected":"")?> >0</option>
	<option value="1" <?=(($children=='1') ? "selected":"")?> >1</option>
	<option value="2" <?=(($children=='2') ? "selected":"")?> >2</option>
</select></p>
<p>Найти: <input type="submit" name="submit"></p>
</form>
<?php
if(!empty($_POST['submit'])){
	$now = strtotime("now");
	$created = filemtime('bgcyprus.txt');
	$dif = ($now - $created)/3600;
	if($dif > 24){
		getfile('http://export.bgoperator.ru/yandex?action=files&flt=100410000049&flt2=100510001075&xml=11','bgcyprus.txt');
	}
	$course = simplexml_load_file('bgcourse.xml');
	$eur = (string) $course -> rate[0]['value'];
	$eur=str_replace('.', ',', $eur);
	$usd = (string) $course -> rate[1]['value'];
	$usd = str_replace('.', ',', $usd);
	$today = new DateTime("now");

	$file = file_get_contents('bgcyprus.txt');
	$baseprice = json_decode($file, true);
	$lenght = count($baseprice['entries']);
	for($i=0; $i<$lenght; $i++){
		if($baseprice['entries'][$i]['date'] == $serchdate){
			$url=$baseprice['entries'][$i]['url'];
			break;
		}
	}
	$url .= "&f1=$resort";
	$url .= "&f3=$hotel";
	$url .= "&f7=$nights";
	$url .= "&f8=$meal";
	$url .= "&p=";
	$i=1;
	while($i<=$adults){
		$url .= ".0010119600";
		$i++;
	}
	$i=1;
	while($i<=$children){
		$url .= ".0010120070";
		$i++;
	}
	/*for($i=1; $i<$adults; $i++){
		$url .= "0010119600";
	}
	for($i=1; $i<$adults; $i++){
		$url .= "0010119600";
	}*/
	//$url .= "&p=0010119600.0010119600";
	$price = json_decode(getfile($url,'bg_cyprus_price.txt',true), true);
	$hotels = simplexml_load_file('bg-cyprus-hotel.xml');
	$hotel = $hotels -> xpath("//hotel[@id=102625943326]");

	//echo "<pre>";
	//print_r($price);
	//echo "</pre>";
	?>
	<table border="1px solid">
	<tr>
		<td>Дата</td>
		<td>Авиакомпания</td>
		<td>Курорт</td>
		<td>Отель</td>
		<td>Номер</td>
		<td>Питание</td>
		<td>Ночей</td>
		<td>Цена</td>
	</tr>
	<?php
	$lenght = count($price['entries']);
	for($i=0; $i<$lenght; $i++){
		if($price['entries'][$i]['prices'][0]['RUR']>0){
			echo "<tr>";
				echo "<td>".$price['entries'][$i]['tour_date']."</td>";
				echo "<td>".$price['entries'][$i]['aircompany']."</td>";
				echo "<td>".substr($price['entries'][$i]['town'], 0, strpos($price['entries'][$i]['town'], '/'))."</td>";
				$hotelid = $price['entries'][$i]['id_hotel'];
				$hotel = $hotels -> xpath("//hotel[@id=$hotelid]");
				echo "<td>{$hotel[0]}</td>";
				echo "<td>".substr($price['entries'][$i]['room'], 0 , -2)."</td>";
				echo "<td>".substr($price['entries'][$i]['room'], -2)."</td>";
				echo "<td>".$price['entries'][$i]['duration']."</td>";
				echo "<td>".$price['entries'][$i]['prices'][0]['RUR']."</td>";
			echo "</tr>";
		}
	}
}
?>
</table>