//mobile browser checked
var mobileBrowser = false

function mbrcheck() {
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) mobileBrowser = true;})(navigator.userAgent||navigator.vendor||window.opera);
	if (mobileBrowser == true) {
		var hiddens = document.getElementsByClassName("m-hidden");
		for (var i = 0; i < hiddens.length; i++) {
			hiddens[i].style.display = "none";
		}
		//LAIKINAI!!!!
		document.getElementById("search-box").style.display = "none";
	}
}

//html templates
var htmls = {};
var names = ["addNote", "addTasks", "createGroup", "shareWindow"];

function loadTemplates() {
	for (var i = 0; i < names.length; i++) {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var response = this.responseText;
				var respUrl = this.responseURL;
				respUrl = respUrl.replace(/.html/g, "");
				// padaryti + 1 pries keliant i serveri
				htmls[respUrl.split("/")[respUrl.search("/") + 2]] = response;
			}
		}
		xmlhttp.open("GET", "/app/views/design/templates/"+ names[i] +".html", true);
		xmlhttp.send();
	}
}

function sidebardet() {
	var width = document.getElementById("side-bar").style.width;
	width = parseInt(width);
	
	if (width > 50) {
		document.getElementById("side-bar").style = "";
		var buttons = document.getElementsByClassName("sidebar-button-name");
		for (var i = 0; i < buttons.length; i++) {
			buttons[i].style.display = "none";
		}
	} else {
		document.getElementById("side-bar").style.width = "200px";
		setTimeout(showTitles, 500);
	}
}

function showTitles() {
	if (document.getElementById("side-bar").style.width == "200px") {
		var buttons = document.getElementsByClassName("sidebar-button-name");
		for (var i = 0; i < buttons.length; i++) {
			buttons[i].style.display = "inline-block";
		}
	}
}

// SP-WINDOW

var bwidth;
var bheight;

function getResolution() {
	bwidth= window.innerWidth;
	bheight= window.innerHeight;
}

function showWindow(title, html, formAct, wtd) {
	if (document.getElementById("sp-window") == null) {
		getResolution();
		var addWindow = document.createElement("DIV");
		addWindow.className = "sp-window";
		addWindow.id = "sp-window";
		addWindow.innerHTML = htmls[html];
		var h = document.getElementsByTagName("body")[0].insertAdjacentHTML('beforeend', addWindow.outerHTML);
		document.getElementById("sp-window-title").innerHTML = title;
		center();
		if (formAct == 'taskForm' && wtd === 'add') {
			addInputArea(true, 'task', {'task':''}, '');
		}
		if (wtd === "add") {
			if (html == "addNote") {
				document.forms["noteForm"].addEventListener('submit', function(event){ saveNote(); event.preventDefault();});
				loadShareTo();
			} else if (html == "addTasks") {
				document.forms["taskForm"].addEventListener('submit', function(event){ saveTask(); event.preventDefault(); });
				loadShareTo();
			} else if (html == "createGroup") {
				document.forms["groupForm"].addEventListener('submit', function(event){ createGroup(); event.preventDefault(); });
				getFriendsList();
			} else if (html == "shareWindow") {
				document.forms["shareForm"].addEventListener('submit', function(event){ shareRecord(event); event.preventDefault(); });
			}
		}
	}
}

function loadShareTo() {
	loadGroups(function(result) {
		var groups = result;
		var selector = document.querySelector('div.sp-window > form > select[name="shareTo"]');
		for (var i = 0; i < groups.length; i++) {
			var option = document.createElement('option');
			option.value = groups[i].groupID
			option.innerHTML = groups[i].groupName;
			selector.add(option);
		}
	});
}

function closeWindow() {
	var body = document.getElementsByTagName("body")[0];
	var win = document.getElementById("sp-window");
	body.removeChild(win);
}

function addInputArea(add, type, inputText, pos) {
	var toAppend, param;
	switch(type) {
		case 'task' : 
			if (inputText.done == 1) param = 'checked';
			else param = '';
			toAppend = '<input type="checkbox" name="done" '+param+'> <input id="taskInput" type="text" name="addedInputArea" placeholder="Your task" data-pos="'+pos+'" value="'+inputText.task+'">';
		break;
		case 'grouptask' : 
			if (inputText.done == 1) param = 'checked';
			else param = '';
			toAppend = '<input type="checkbox" name="done" '+param+'> <input id="taskInput" type="text" name="addedInputArea" placeholder="Your task" data-pos="'+pos+'" value="'+inputText.task+'">';
		break;
		case 'group' :
			toAppend = '<input id="taskInput" type="text" name="addedInputArea" placeholder="Friend\'s name" data-pos="'+pos+'" value="'+inputText.task+'">';
	}
	if (add == true) {
		var container = document.createElement("div");
		container.className = "sp-window-taskinp";
		container.innerHTML = toAppend;
		document.getElementById("taskarea").appendChild(container);
		
		if (type == 'task') {
			if (inputText.task.length > 0) container.innerHTML += '<button type="button"><img src="/app/views/design/icons/delete.png" alt="delete icon"></button>';
		} else if (type == 'group') {
			if (pos.length == 0) inputAreaOnkeyupper(); 
		} else if (type == 'grouptask') {
			
		}
		center();
	}
}

function center() {
	var spWindow = document.querySelector("div.sp-window");
	var inps = document.getElementsByName("addedInputArea");
	var height = spWindow.offsetHeight;
	var width = spWindow.offsetWidth;
	document.getElementsByClassName("sp-window-notetext")[0].style.height = (height - 150) + 'px';
	for (var i = 0; i < inps.length; i++) {
		inps[i].style.width = (width- 110) + 'px';
	}
}

// note/task card hover options

function noteOverLeave(event) {
	var actions = this.querySelector('div.actions');
	if (actions.style['display'] == '') {
		actions.style['display'] = 'inline';
	} else {
		actions.style['display'] = '';
	}
}

function determButton(event) {
	if (this.matches('button[type="button"]')) {
		var index = this.attributes[0].value;
		switch(index) {
				case '0':
					editNote(event);
					break;
				case '1':
					deleteRecord(event);
					break;
				case '2': 
					determID(event);
					showWindow('Share to', 'shareWindow', 'shareForm', 'add');
					break;
				case '3':
					alert('Group editing functionality will be available in the future versions');
					break;
				case '4':
					deleteGroup(event);
		}
	}
}

//content registry
var contentRegistry = [], prevLength = 0;

function contentHandler(toProcess) {
	var toProcess = toProcess;
	if (toProcess.type == "0" && typeof toProcess.groupId !== "undefined") {
		toProcess.type = "2";
	} else if (toProcess.type == "1" && typeof toProcess.groupId !== "undefined") {
		toProcess.type = "3";
	}
	return toProcess;
}

function editRegistry(loadedContent) {
	var loadedHandled = contentHandler(loadedContent);
	for (var i = 0; i < contentRegistry.length; i++) {
		if (loadedHandled.noteid == contentRegistry[i].noteid && loadedHandled.type == contentRegistry[i].type) {
			contentRegistry[i] = loadedHandled;
		}
	}
}

function loadToRegistry(loadedContent) {
	contentRegistry.push(loadedContent);
}	

function refreshContent() {
	var regLength = contentRegistry.length;
	
	var i;
	if (prevLength == 0) i = 0;
	else if (prevLength < regLength) i = prevLength;
	
	for (i; i < regLength; i++) {
		var showdest;
		var card = document.createElement('div');
		if (contentRegistry[i].type == 0 || contentRegistry[i].type == 1) {
			showdest = document.getElementById("noteLoadDest");
			var toAppend = '<div class="card note" data-individ="'+ contentRegistry[i].noteid +'" data-uiid="'+i+'">\
				<div class="actions"><button data-index="0" type="button"><img src="/app/views/design/icons/edit.png" alt="edit icon"></button> \
				<button data-index="2" type="button"><img src="/app/views/design/icons/share.png" alt="share icon"></button>\
				<button data-index="1" type="button"><img src="/app/views/design/icons/delete.png" alt="delete icon"></button></div>\
				<div class="card-body">';
		} else if (contentRegistry[i].type == 2 || contentRegistry[i].type == 3) {
			showdest = document.getElementsByClassName("groups")[0];
			var toAppend = '<div class="card note" data-recid="'+ contentRegistry[i].noteid +'" data-shared="'+ contentRegistry[i].groupId +'" data-uiid="'+i+'">\
			<div class="actions"><button data-index="0" type="button"><img src="/app/views/design/icons/edit.png" alt="edit icon"></button> \
			<button data-index="1" type="button"><img src="/app/views/design/icons/delete.png" alt="delete icon"></button></div>\
			<div class="card-body"><p style="font-size: 0.8rem; opacity: 0.7;">'+ contentRegistry[i].groupName +'</p>';
		}
		toAppend += '<h5 class="card-title">'+ contentRegistry[i].title +'</h5>';
		if (contentRegistry[i].type == 0 || contentRegistry[i].type == 2) {
			toAppend += '<p class="card-text">'+ contentRegistry[i].content +'</p>';
		} else if (contentRegistry[i].type == 1 || contentRegistry[i].type == 3) {
			for (var u = 0; u < contentRegistry[i].content.length; u++) {
				if (contentRegistry[i].content[u].done == 1)
					toAppend += '<div class="checkBoxDiv"><img src="/app/views/design/icons/check.png" alt="check icon"></div><p>'+ contentRegistry[i].content[u].task +'</p>';
				else toAppend += '<div class="checkBoxDiv"></div><p>'+ contentRegistry[i].content[u].task +'</p>';
			}
		}
		toAppend += '</div></div>';
		card.innerHTML = toAppend;
		showdest.insertAdjacentHTML('afterbegin', card.outerHTML);
	}
	addEditNoteActions(); //edeleg.js
	prevLength = regLength;
}

function editContent(act, uiid) {
	if (act == 'del') {
		contentRegistry.splice(uiid, 1);
		var card = document.querySelector('div[data-uiid="'+uiid+'"]');
		card.remove();
	} else if (act == 'edit') {
		var card = document.querySelector('div[data-uiid="'+uiid+'"] > div.card-body');
		if (contentRegistry[uiid].type == 0 || contentRegistry[uiid].type == 2) {
			if (contentRegistry[uiid].type == 0) {
				card.childNodes[0].innerHTML = contentRegistry[uiid].title;
				card.childNodes[1].innerHTML = contentRegistry[uiid].content;
			} else {
				card.childNodes[1].innerHTML = contentRegistry[uiid].title;
				card.childNodes[2].innerHTML = contentRegistry[uiid].content;
			}
		} else if (contentRegistry[uiid].type == 1 || contentRegistry[uiid].type == 3) {
			if (contentRegistry[uiid].type == 1) {
				card.childNodes[0].innerHTML = contentRegistry[uiid].title;
			} else {
				card.childNodes[1].innerHTML = contentRegistry[uiid].title;
			}
			var tasks = document.querySelectorAll('div[data-uiid="'+uiid+'"] > div.card-body > p:not([style])').forEach(e => e.parentNode.removeChild(e));
			var checkboxes = document.querySelectorAll('div[data-uiid="'+uiid+'"] > div.card-body > div.checkBoxDiv').forEach(e => e.parentNode.removeChild(e));
			for (var i = 0; i < contentRegistry[uiid].content.length; i++) {
				var text = document.createElement("p");
				text.innerHTML = contentRegistry[uiid].content[i].task;
				var checkbox = document.createElement("div");
				checkbox.className = 'checkBoxDiv';
				if (contentRegistry[uiid].content[i].done == 1) {
					checkbox.innerHTML = '<img src="/app/views/design/icons/check.png" alt="check icon">';
				}
				card.appendChild(checkbox);
				card.appendChild(text);
			}
		}
	}	   
}

//account button in header
function accountActions(event) {
	var query = document.querySelector('div.accAct');
	if (query == null) {
	var accAct = document.createElement("div");
	accAct.innerHTML = '<div class="row"><div class="col-5">\
	<img class="circularIMG" src="/app/views/design/icons/defaultAvatar.png" del="default profile picture"></div>\
	<div class="col-7"><p>Name Surname</p> <a href="/app/profile">More</a><br>\
	<a href="/account/logout">Log out</a><div></div>';
	accAct.className = 'accAct';
	document.querySelector('body').appendChild(accAct);
	} else {
		query.remove();
	}
	event.preventDefault();
}

function manageGroups() {
	loadGroups(function(result) {
		var groups = result;
		var showdest = document.querySelector('div.manageGroups');
		for (var i = 0; i < groups.length; i++) {
			var groupCard = document.createElement('div');
			var groupName = document.createElement('h5');
			var members = document.createElement('p');
			groupCard.className = 'card groupCard';
			groupCard.setAttribute('data-groupId', groups[i].groupID);
			groupName.className = 'card-title';
			members.className = 'card-text';
			groupName.innerHTML = groups[i].groupName;
			for (var u = 0; u < groups[i].members.length; u++) {
				members.innerHTML += groups[i].members[u].name + ', ';
			}
			groupCard.innerHTML = '<div class="actions" style="">\
			<button data-index="3" type="button"><img src="/app/views/design/icons/edit.png" alt="edit icon"></button>\
			<button data-index="4" type="button"><img src="/app/views/design/icons/delete.png" alt="delete icon"></button></div>\
			<div class="card-body">'+ groupName.outerHTML + members.outerHTML +'</div>';
			showdest.insertAdjacentHTML('afterbegin', groupCard.outerHTML);
		}
		console.log(groups);
		addManageGroupActions();
	});
}

window.addEventListener("resize", function() {
	getResolution();
	center();
	//kai per mazas ekranas slepti
	/*if (bwidth <= 750) { 
		document.getElementById("rightside-bar").style.display = "none";
	} else {
		document.getElementById("rightside-bar").style.display = "";
	}*/ 
});

/*state handler
function stateHandler(page) {
	history.pushState({ tab: page}, page, "/app/show/?tab=" + page);
	if (page == 'groups') {
		document.querySelector('div.content').style['display'] = 'none';
		document.querySelector('div.manageGroups').style['display'] = '';
		manageGroups();
	} else {
		document.querySelector('div.content').style['display'] = '';
		document.querySelector('div.manageGroups').style['display'] = 'none';
	}
}*/
	