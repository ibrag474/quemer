//The beggining group, notes function

function loadNotes(context, callback) {
	let headers = { name: [], body: [] };
	ajax('GET', 'v1/notes', null, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, result);
			} else callback(context, null);
		} else callback(context, null);
	});
}

function loadNote(context, individ, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": individ};
	ajax('GET', 'v1/notes/note', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, result);
			} else callback(context, null);
		} else callback(context, null);
	});
}

function loadSharedNote(context, individ, groupid, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": individ, "groupid": groupid};
	ajax('GET', 'v1/notes/sharednote', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, result);
			} else callback(context, null);
		} else callback(context, null);
	});
}

function loadTask(context, individ, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": individ };
	ajax('GET', 'v1/notes/task', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, result);
			} else callback(context, null);
		} else callback(context, null);
	});
}

function loadSharedTask(context, individ, groupid, callback) {
	let headers = { name: [], body: [] };
	let data = {"id": individ, "groupid": groupid};
	ajax('GET', 'v1/notes/sharedtask', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, result);
			} else callback(context, null);
		} else callback(context, null);
	});
}

function submitNote(context, note, action, callback) {
	let method = action;
	switch(note.type) {
		case '0' :
			if (!note.hasOwnProperty('groupId')) method += 'PersonalNote';
			else method += 'GroupNote';
		break;
		case '1' :
			if (!note.hasOwnProperty('groupId')) method += 'PersonalTask';
			else method += 'GroupTask';
		break;
		case '2' :
			method += 'GroupNote';
		break;
		case '3' :
			method += 'GroupTask';
		break;
	}
	window[method](note, function(status, result) {
		if (status == 200) {
			if (typeof result != 'undefined')
				callback(context, true, result);
			else callback(context, true);
		} else callback(context, false);
	});
}
//adds personal and group tasks
function addPersonalNote(note, callback) {
	let headers = { name: [], body: [] };
	let data = {'title': note.title, 'content': note.content, 'shareTo': note.shareTo};
	ajax('POST', 'v1/notes/note', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(status, result);
			} else callback(status);
		} else callback(status);
	});
}
//adds personal and group tasks
function addPersonalTask(note, callback) {
	let headers = { name: [], body: [] };
	let data = {'title': note.title, 'content': note.content, 'shareTo': note.shareTo};
	ajax('POST', 'v1/notes/task', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(status, result);
			} else callback(status);
		} else callback(status);
	});
}

function editPersonalNote(note, callback) {
	let headers = { name: [], body: [] };
	let data = {'title': note.title, 'content': note.content, 'individ': note.individ};
	ajax('PUT', 'v1/notes/note', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(status);
			} else callback(status);
		} else callback(status);
	});
}

function editPersonalTask(note, callback) {
	let headers = { name: [], body: [] };
	let data = {'title': note.title, 'content': note.content, 'individ': note.individ};
	ajax('PUT', 'v1/notes/task', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(status);
			} else callback(status);
		} else callback(status);
	});
}

function editGroupTask(note, callback) {
	let headers = { name: [], body: [] };
	let data = {'title': note.title, 'content': note.content, 'individ': note.individ, 'groupid': note.groupId};
	ajax('PUT', 'v1/notes/task', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(status);
			} else callback(status);
		} else callback(status);
	});
}

function shareRecord(context, recID, groupName, callback) {
	let headers = { name: [], body: [] };
	let data = {'groupName': groupName, 'recID': recID};
	ajax('POST', 'v1/notes/share', data, headers, function(status, result) {
		if (status == 200) {
			if (result != null) {
				callback(context, status, result);
			} else callback(context, status);
		} else callback(context, status);
	});
}

function deleteNote(context, note, type, callback) {
	let headers = { name: [], body: [] };
	let data;
	if (note.groupId == 'undefined') 
		data = { 'individ': note.individ };
	else data = { 'individ': note.individ, 'groupid': note.groupId };
	ajax('DELETE', 'v1/notes/' + type, data, headers, function(status, result) {
		callback(context, status);
	});
}

function loadGroups(context, callback) {
	let headers = { name: [], body: [] };
	ajax('GET', 'v1/groups/', null, headers, function(status, result) {
		callback(context, status, result);
	});
}

function loadGroup(context, groupID, callback) {
	let headers = { name: [], body: [] };
	let data = {groupid: groupID};
	ajax('GET', 'v1/groups/group', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

function loadFriends(context, callback) {
	let headers = { name: [], body: [] };
	ajax('GET', 'v1/people/known', null, headers, function(status, result) {
		callback(context, status, result);
	});
}

function postGroup(context, group, callback) {
	let headers = { name: [], body: [] };
	let data = {title: group.groupName, content: group.groupMembers};
	ajax('POST', 'v1/groups/group', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

function putGroup(context, group, callback) {
	let headers = { name: [], body: [] };
	let data = {title: group.groupName, content: group.groupMembers, groupid: group.groupID};
	ajax('PUT', 'v1/groups/group', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

function deleteGroup(context, group, callback) {
	let headers = { name: [], body: [] };
	let data = {groupid: group};
	ajax('DELETE', 'v1/groups/group', data, headers, function(status, result) {
		callback(context, status, result);
	});
}

//the end of group, notes function















