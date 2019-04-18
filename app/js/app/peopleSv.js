function searchPeople(context, name, callback) {
	let headers = { name: [], body: [] };
	let data = {"name": name};
	ajax('GET', 'v1/people/find', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, result);
			} else callback(context, null);
		} else callback(context, null);
	});
}

function invitePerson(context, personID, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": personID};
	ajax('POST', 'v1/people/invite', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

function loadKnown(context, callback) {
	let headers = { name: [], body: [] };
	ajax('GET', 'v1/people/known', null, headers, function(status, result) {
		callback(context, status, result);
	});
}

function deleteKnown(context, knownID, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": knownID};
	ajax('DELETE', 'v1/people/invite', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

function acceptInvite(context, knownID, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": knownID};
	ajax('PUT', 'v1/people/invite', data, headers, function(status, result) {
		callback(context, status, result);
	});
}