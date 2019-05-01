window.onload = function() {
	let href = window.location.href;
	href = decodeURI(href);
	let json = href.split("=")[1];
	json = JSON.parse(json).hash;
	submitActivationCode(json, (status, result) => {
		if (status == 200) {
			alert("Your account is now activated.");
			window.location.replace("/account/login");
		} else {
			alert(result.message + " " + result.exception);
		}
	});
};