document.getElementById('good').addEventListener('click', function(){
	var u = this.dataset.url;
	var aj = new XMLHttpRequest();
	aj.open('GET', u, true);
	aj.onreadystatechange = function(){
		if(aj.readyState == 4 && aj.status == 200){
			try{
				var json = JSON.parse(aj.responseText);
			}catch(e){
				alert('error');
			}
			
			if(json.result == 0){
				document.getElementById('good').textContent = '赞(' + json.good + ')';
			}else{
				alert('u zan le');
			}
		}
	}
	aj.send();
});