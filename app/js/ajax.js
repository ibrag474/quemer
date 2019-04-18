function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return false;
}

function ajax(type, address, json, headers, callback) {
	var data = json;
	var jsondata = encodeURIComponent(JSON.stringify(data));
	
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 401) {
				logOUT();
			}
			var response = this.responseText;
			renewJWT(xmlhttp.getAllResponseHeaders());
			callback(this.status, ojson(response));
		}
	}
	if (json == null)
		xmlhttp.open(type, '/api/' + address, true);
	else xmlhttp.open(type, '/api/' + address + '?json=' + jsondata, true);
	xmlhttp.setRequestHeader("Content-type", "application/json");
	let jwt = getCookie('jwt');
	if (jwt != false) xmlhttp.setRequestHeader("Authorization", jwt);
	for (var i = 0; i < headers.name.length; i++) {
		xmlhttp.setRequestHeader(headers.name[i], headers.body[i]);
	}
	xmlhttp.send();
}

function logOUT() {
	document.cookie = "jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	if (window.location.pathname != '/account/login') {
		window.location = '/account/login';
	}
}

function renewJWT(header) {
	let headers = header.trim().split(/[\r\n]+/);
	let headerMap = {};
    headers.forEach(function (line) {
		var parts = line.split(': ');
		var header = parts.shift();
		var value = parts.join(': ');
		headerMap[header] = value;
    });
	if (headerMap.hasOwnProperty('authorization')) {
		document.cookie = "jwt="+ headerMap['authorization'] +"; path=/; sameSite=strict;";
	}
}

function ojson(data) {
	let json = JSON.parse(data);
	return json;
}