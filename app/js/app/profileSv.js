function changePassword(context, password, callback) {
	let headers = { name: [], body: [] };
	let data = {"password": password[0], "newPassword": password[1]};
	ajax('PUT', 'v1/auth/password', data, headers, function(status, result) {
		callback(context, status, result);
	});
}