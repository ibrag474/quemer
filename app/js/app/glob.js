function loadMe(context, callback) {
	let headers = { name: [], body: [] };
	ajax('GET', 'v1/users/me', null, headers, function(status, result) {
		callback(context, status, result);
	});
}