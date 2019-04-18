window.onload = function() {
	mbrcheck(); //appUI.js
	loadTemplates(); //appUI.js
};

window.onpopstate = function() {
	//checkUrl();
};
/**
function checkUrl() {
	var path = window.location.pathname;
	if (path == '/app/people/') {
		var params = (new URL(document.location)).searchParams;
		var page = params.get("page");
		switch (page) {
			case 'search' :
			switchTabs(0);
			break;
			case 'known' : 
			switchTabs(1);
		}
	} else if (path == '/app/show' || path == '/app/show/') {
		var params = (new URL(document.location)).searchParams;
		var page = params.get("tab");
		if (page == null) page = 'notes';
		stateHandler(page);
	}
}*/