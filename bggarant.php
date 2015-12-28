<?php
class Bggarant{
	private $now, $prices, $countries;
	private $usd, $eur;
	public $table;
	
	//cunstructs the object and checks up to date course prices
	public function __construct(){
		$this->now = new DateTime("now");
		//auto check course
		$this->checkFile('http://export.bgoperator.ru/auto/auto-kurs.xml', 'bgcourse.xml');
		$course = simplexml_load_file('bgcourse.xml');
		$this->eur = (string) $course -> rate[0]['value'];
		$this->eur = str_replace('.', ',', $this->eur);
		$this->usd = (string) $course -> rate[1]['value'];
		$this->usd = str_replace('.', ',', $this->usd);
	}
	
	//checks that file wasn't loaded more than an hour(3600 seconds)
	//it is necessary because data is cached in files
	public function checkFile($url, $file){
		//checks if file exists
		if(file_exists($file)){
			$created = filemtime($file);
		} else $created = 0;
		$dif = ($this->now->format("U") - $created)/3600;
		if($dif > 3){
			Functions::getFile($url, $file);
		}
	}
	
	//gets prices and sorts them in the ascending order
	public function getPrices(){
		$file = file_get_contents('bggarant.txt');
		$this->prices = json_decode($file, true);
		foreach($this->prices as $key => $row){
			$price[$key] = $row['RUR'];
		}
		array_multisort($price, SORT_ASC, SORT_NUMERIC, $this->prices);
	}
	
	//calculates prices according to rubbles
	private function calcCurrency($val, $amount){
		switch($val){
			case 'EUR':
				$cur = $this->eur;
				break;
			case 'USD':
				$cur = $this->usd;
				break;
			case 'RUR':
				$cur = 1;
				break;
		}
		return round( $amount*$cur*1.02 );
	}
	
	//draws table, but doesn't show it
	//it is necessary because I want to store all the countries in the array
	//these countries will be later shown in the menu, but I need to collect them first
	public function getTable($country){
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
		foreach($this->prices as $row){
			$date = new DateTime($row['data']);
			$dif = ($date->format("U") - $this->now->format("U"))/3600/24;
			//we are interested only in the later departures that are more than 24 hours in the future
			if( $dif > 1 ){	
				//price calculation according to the internal BiblioGlobus currency
				$price = $this->calcCurrency($row['val'], $row['amount']);
				//collecting all countries
				$this->countries[] = $row['cntryName'];
				if(($row['cntryName'] == $country and $price < 50000) or  (empty($country) and $price < 50000)){
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
		//table is public value, so it is possible to call it anywhere
		$this->table = ob_get_contents();
		ob_end_clean();
	}
	
	//shows ajax menu with all previously collected unique countries
	public function showMenu(){
		$unique_countries = array_unique($this->countries, SORT_STRING);
		echo "<p align='center'>";
		echo "<span style='color:#388CBC; cursor:pointer;' onClick=\"showbggarant('');\"> &rarr;Все страны</span>&nbsp;&nbsp;&nbsp;";
		foreach($unique_countries as $country){
			echo "<span style='color:#388CBC; cursor:pointer;' onClick=\"showbggarant('$country');\"> &rarr;".$country."</span>&nbsp;&nbsp;&nbsp;";
		}
		echo "</p>";
	}
}