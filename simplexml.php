<?php
$file = simplexml_load_file('bghotels.xml');
$count = $file->count();
for($i=0; $i<$count; $i++){
	if($file->hotel[$i]->attributes()->countryKey != '100410000049'){
		 unset($file->hotel[$i]);
	}
}
$file->asXML('bghotelcyprus.xml');