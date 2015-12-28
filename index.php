<?php
function my_autoloader($name){
    require_once(strtolower($name).'.php');
}
spl_autoload_register('my_autoloader');
?>
<!DOCTYPE html>
<html>
<head>
<title>Лучшие предложения БиблиоГлобуса</title>
</head>
<body>
<script type="text/javascript" src="ajax.js"></script>
<div id='bggarant'>
<h2 style='color:#1E73BE;' align="center">Спецпредложения с мгновенным подтверждением</h2>
<?php
	@$country = Functions::cleanvalue($_GET['country']);
	$bg = new Bggarant();
	$bg->checkFile('http://export.bgoperator.ru/auto/auto-spo-100510001075v2-124331253701.js','bggarant.txt');
	$bg->getPrices();
	$bg->getTable($country);
	$bg->showMenu();
	echo $bg->table;
?>
</div>
</body>
</html>