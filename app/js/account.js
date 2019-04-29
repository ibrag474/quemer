/*function submitLogin() {
		var email = document.forms["loginForm"]["email"].value;
		var password = document.forms["loginForm"]["password"].value;
		var data = { "email":email, "password":password };
		var jsondata = JSON.stringify(data);
		
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var response = this.responseText;
			var myObj = JSON.parse(response);
			if (myObj["action"] == 'redirect') {
				location.replace(myObj["message"]);
			} else if (myObj["action"] == 'message') {
				document.querySelector('p[id="res"]').innerHTML = myObj["message"];
			}
		}
	}
	xmlhttp.open("POST", "login/?param=" + jsondata, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(); 
}*/

function submitLogin(context, formData, callback) {
	let headers = { name: ['Authorization'], body: [btoa(unescape(encodeURIComponent(formData.email + ':' + formData.password)))]};
	ajax('POST', 'v1/auth/login', null, headers, function(status, result) {
		if (status == 200 &&result.jwt != null)
			document.cookie = "jwt="+ result.jwt +"; path=/; sameSite=strict;";
		callback(context, status, result);	
	});
}

function checkpswdmatch() {
	var email = document.forms["registerForm"]["email"].value;
	var name = document.forms["registerForm"]["name"].value;
	var password = document.forms["registerForm"]["password"].value;
	var repassword = document.forms["registerForm"]["re-password"].value;
	var formData = {
		email:email,
		name:name,
		password:password
	};
	if (repassword === password) {
		submitSignOn(formData);
	} else {
		document.getElementById("errmsg").innerHTML  = "Passwords does not match!";
		document.getElementById("pswd").value = "";
		document.getElementById("repswd").value = "";
	}
}

function submitSignOn(context, formData, callback) {
	var data = { "email":formData.email, "password":formData.password, "name":formData.name, surname: formData.surname };
	var jsondata = JSON.stringify(data);
		
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			var response = this.responseText;
			var myObj = JSON.parse(response);
			callback(context, this.status, myObj);
		}
	}
	xmlhttp.open("POST", "/api/v1/auth/register/?json=" + jsondata, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(); 
}

function forgotPswd(context, email, callback) {
	var data = { "email":email };
	var jsondata = JSON.stringify(data);

	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			const response = this.responseText;
			const myObj = JSON.parse(response);
			callback(context, this.status, myObj);
		}
	}
	xmlhttp.open("POST", "/api/v1/auth/restore/?json=" + jsondata, true);
	xmlhttp.setRequestHeader("Content-type", "application/json");
	xmlhttp.send();
}

function submitPswdReset(context, code, pswd, repswd, callback) {
	var code = code
	var pswd = pswd
	var repswd = repswd
	var data = { "code":code, "password":pswd };
	var jsondata = JSON.stringify(data);
		
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			const response = this.responseText;
			const myObj = JSON.parse(response);
			callback(context, this.status, myObj);		
		}
	}
	xmlhttp.open("PUT", "/api/v1/auth/restore/?json=" + jsondata, true);
	xmlhttp.setRequestHeader("Content-type", "application/json");
	xmlhttp.send();
}

function submitResendActCode(context, email, callback) {
	var data = { "email":email };
	var jsondata = JSON.stringify(data);

	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			const response = this.responseText;
			const myObj = JSON.parse(response);
			callback(context, this.status, myObj);
		}
	}
	xmlhttp.open("POST", "/api/v1/auth/activation/?json=" + jsondata, true);
	xmlhttp.setRequestHeader("Content-type", "application/json");
	xmlhttp.send();
}






