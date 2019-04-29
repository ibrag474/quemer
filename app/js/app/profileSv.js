function changePassword(context, password, callback) {
	let headers = { name: [], body: [] };
	let data = {"password": password[0], "newPassword": password[1]};
	ajax('PUT', 'v1/auth/password', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

function changeAccDetails(context, details, callback) {
	let headers = { name: [], body: [] };
	let data = {"surname": details.surname};
	ajax('PUT', 'v1/users/details', data, headers, function(status, result) {
		callback(context, status, result);
	});
}