function showbggarant(str){
	var XMLHttpgarant=new window.XMLHttpRequest();
	XMLHttpgarant.onreadystatechange = function(){
		if(XMLHttpgarant.readyState == 4){
			document.getElementById("bggarant").innerHTML = XMLHttpgarant.responseText;
		}
	}
	XMLHttpgarant.open("GET", "index.php?country="+str,true);
	XMLHttpgarant.send();
}