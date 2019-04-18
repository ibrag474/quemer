//assign delete buttons to tasks
function delegateTasks() {
	var taskinputs = document.querySelectorAll('div.sp-window-taskinp');
	for (var i = 0; i < taskinputs.length; i++) {
		taskinputs[i].addEventListener('click', deleteTask );
	}
}
//assign note/task cards to editNote()
function addEditNoteActions() {
	var noteCard = document.querySelectorAll('div.note');
	for (var i = 0; i < noteCard.length; i++) {
		noteCard[i].addEventListener('mouseenter', noteOverLeave );
		noteCard[i].addEventListener('mouseleave', noteOverLeave );
	}
	var buttons = document.querySelectorAll('div.actions > button[type="button"]');
	for (var i = 0; i < buttons.length; i++) {
		buttons[i].addEventListener('click', determButton );
	}
}

function addManageGroupActions() {
	var groupCard = document.querySelectorAll('div.groupCard');
	for (var i = 0; i < groupCard.length; i++) {
		groupCard[i].addEventListener('mouseenter', noteOverLeave );
		groupCard[i].addEventListener('mouseleave', noteOverLeave );
	}
	var buttons = document.querySelectorAll('div.actions > button[type="button"]');
	for (var i = 0; i < buttons.length; i++) {
		buttons[i].addEventListener('click', determButton );
	}
}

//Groups creation window
function delegateNameHint() {
	var buttons = document.querySelectorAll('div.sp-window-notetext > div > button');
	for (var i = 0; i < buttons.length; i++) {
		buttons[i].addEventListener('click', addPerson );
	}
}