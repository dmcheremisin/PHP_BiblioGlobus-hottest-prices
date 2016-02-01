# Hottest prices for travelling in Russia

Instructions:
* upload all files to your server
* fill in functions.php your username and password
* visit index.php

It uses ajax for updating user requests.

It is assumed that your are interested in prices from Saint-Petersburg.
If you need Moscow or other cities, you should change in the index.php line:
$bg->checkFile('http://export.bgoperator.ru/auto/auto-spo-100510001075v2-124331253701.js','bggarant.txt');
For example, for Moscow you should change url to http://export.bgoperator.ru/auto/auto-spo-100510000863v2-124331253701.js.
All data will be stored in a text file 'bggarant.txt' and automatically updated every 1 hour.
All prices are automatically converted in rubles according to Biblio Globus internal course.

For further information, you should visit http://export.bgoperator.ru/load-xml-prices.html.
Everything works stable on the 01.02.2015
