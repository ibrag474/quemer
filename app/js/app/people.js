const cel = React.createElement;

class People extends React.Component {
	constructor() {
		super();
		this.state = {};
	}
	
	render() {
		return [
			cel(Header, {key: 'header'}, null),
			cel(Tab, {key: 'tab'}, null),
		];	
	}
}

class Tab extends React.Component {
	constructor() {
		super();
		this.state = {current: 'search-btn'};
		this.switchTab = this.switchTab.bind(this);
	}
	
	switchTab(e) {
		const tab = e.currentTarget.attributes['id'].value;
		if (this.state.current != tab) {
			e.currentTarget.style.background = '#32d8ca';
			document.querySelector('button[id="'+ this.state.current +'"]').style.background = '';
			this.setState({current: tab});
		}
	}
	
	render() {
		const tab = determTab(this.state.current);
		return [
			cel('div', {key: 'tab-switcher', className: 'tab-switcher'}, [
				cel('button', {key: 'tab-switcher-btn0', id: 'search-btn', className: 'searchTab-button', style: {background: '#32d8ca'}, onClick: this.switchTab}, 'Search'),
				cel('button', {key: 'tab-switcher-btn1', id: 'known-btn', className: 'knownTab-button', onClick: this.switchTab}, 'Known'),
			]),
			tab
		];	
	}
}

function determTab(current) {
	switch (current) {
		case 'search-btn' :
			return cel(SearchPeople, {key: 'SearchPeople'}, null);
			break;
		case 'known-btn' :
			return cel(KnownPeople, {key: 'KnownPeople'}, null);
	} 
}

class SearchPeople extends React.Component {
	constructor() {
		super();
		this.state = {name: '', people: []};
		this.searchPeople = this.searchPeople.bind(this);
		this.handleName = this.handleName.bind(this);
		this.invitePerson = this.invitePerson.bind(this);
	}
	
	handleName(e) {
		this.setState({name: e.currentTarget.value});
	}
	
	searchPeople(e) {
		const name = this.state.name;
		searchPeople(this, name, (context, result) => {
			this.setState({people: result});
		});
		e.preventDefault();
	}
	
	invitePerson(personID) {
		let people = this.state.people;
		const toEditIndex = people.findIndex(person => (person.id == personID));
		invitePerson(this, personID, (context, status, result) => {
			if (status == 200) {
				let edited = people[toEditIndex];
				edited.status = '1';
				people.splice([toEditIndex], 1);
				people.unshift(edited);
				context.setState({people: people});
			} else if (status == 400) {
				alert(result.exception);
			}
		});
	}
	
	render() {
		return cel('div', {key: 'search-people-tab', className: 'search-people tab'}, [
			cel('form', {key: 'searchForm', name: 'peopleSearchForm', onSubmit: this.searchPeople},[
				cel('input', {key: 'searchForm-input0', type: 'text', name: 'name', placeholder: 'Search people', onChange: this.handleName}, null),
				cel('input', {key: 'searchForm-input1',type: 'submit', name: 'submit', value: 'Search'}, null),
			]),
			cel('div', {key: 'people-result', className: 'people-results'}, cel(FoundPeopleCards, {key: 'PeopleCards', people: this.state.people, action: this.invitePerson}, null))
		]);	
	}
}

class KnownPeople extends React.Component {
	constructor() {
		super();
		this.state = {people: []};
		this.unInvite = this.unInvite.bind(this);
		this.acceptInvite = this.acceptInvite.bind(this);
	}
	
	componentDidMount() {
		loadKnown(this, (context, status, result) => {
			if (status == 200) {
				context.setState({people: result});
			} /*else {
				alert(result.exception);
			}*/
		});
	}
	
	unInvite(knownid) {
		deleteKnown(this, knownid, (context, status, result) => {
			if (status == 200) {
				let people = this.state.people;
				const userIndex = people.findIndex(person => person.id == knownid);
				people.splice(userIndex, 1);
				context.setState({people: people});
			} else {
				alert(result.exception);
			}
		});
	}
	
	acceptInvite(knownid) {
		acceptInvite(this, knownid, (context, status, result) => {
			if (status == 200) {
				let people = this.state.people;
				const userIndex = people.findIndex(person => person.id == knownid);
				people[userIndex].areFriends = '1';
				context.setState({people: people});
			} else {
				alert(result.exception);
			}
		}); 
	}
	
	
	render() {
		const people = this.state.people;
		return cel('div', {key: 'known-people-result', className: 'people-results tab'}, cel(KnownPeopleCards, {key: 'KnownPeopleCards', people: people, unInvite: this.unInvite, acceptInvite: this.acceptInvite, declineInvite: this.unInvite, forgetKnown: this.unInvite}, null));	
	}
}

class KnownPeopleCards extends React.Component {
	constructor(props) {
		super(props);
		this.state = {people: this.props.people};
		this.unInvite = this.unInvite.bind(this);
		this.acceptInvite = this.acceptInvite.bind(this);
		this.declineInvite = this.declineInvite.bind(this);
		this.forgetKnown = this.forgetKnown.bind(this);
	}
	
	unInvite(e) {
		const knownid = e.currentTarget.parentElement.attributes['data-id'].value;
		this.props.unInvite(knownid);
	}
	
	acceptInvite(e) {
		const knownid = e.currentTarget.parentElement.attributes['data-id'].value;
		this.props.acceptInvite(knownid);
	}
	
	declineInvite(e) {
		const knownid = e.currentTarget.parentElement.attributes['data-id'].value;
		this.props.declineInvite(knownid);
	}
	
	forgetKnown(e) {
		const knownid = e.currentTarget.parentElement.attributes['data-id'].value;
		this.props.forgetKnown(knownid);
	}
	
	
	render() {
		const people = this.props.people;
		let data = people.map((person) => {
			let userdata = {status: '', action: '', friendid: '', button: ''};
				if (person.areFriends == 0 && person.userid1 == person.myid) {
					userdata.status = 'Invited';
					userdata.action = 'Uninvite';
					userdata.friendid = person.userid2;
					userdata.button = cel('button', {key: 'action-btn', onClick: this.unInvite}, userdata.action);
				} else if (person.areFriends == 0 && person.userid1 != person.myid) {
					userdata.status = 'You have been invited';
					userdata.action = ['Accept', 'Decline'];
					userdata.friendid = person.userid1;
					userdata.button = [cel('button', {key: 'action-btn0', onClick: this.acceptInvite}, userdata.action[0]), cel('button', {key: 'action-btn1', onClick: this.declineInvite}, userdata.action[1])];
				} else if (person.areFriends == 1) {
					userdata.status = 'Known';
					userdata.action = 'Forget';
					userdata.friendid = person.myid == person.userid1 ? person.userid2 : person.userid1;
					userdata.button = cel('button', {key: 'action-btn', onClick: this.forgetKnown}, userdata.action);
				}
			return userdata;
		});
		let card = people.map((person, index) => {
				return cel('div', {key: 'person-card' + index, className: 'person', 'data-id': person.id}, [
					cel('img', {key: 'profile-picture', src: '/app/views/design/icons/defaultAvatar.png', style: {width: '150px', height: '150px'}}, null),
					cel('p', {key: 'name'}, person.name),
					cel('p', {key: 'statusp', id: 'person-status'}, data[index].status),
					data[index].button
				])
		});
		return card;
	}
}

class FoundPeopleCards extends React.Component {
	constructor(props) {
		super(props);
		this.state = {people: this.props.people};
		this.handleAction = this.handleAction.bind(this);
	}
	
	handleAction(e) {
		const personID = e.currentTarget.parentElement.attributes['data-userid'].value;
		this.props.action(personID);
	}
	
	render() {
		const people = this.props.people;
		if (people != null) {
			return people.map((person) => {
				let buttonValue = person.status == 0 ? 'Invite' : person.status == 1 ? 'Invited' : person.status == 2 ? 'Known' : 'SBICUY';
				let card = cel('div', {key: 'person-card', className: 'person', 'data-userid': person.id}, [
					cel('img', {key: 'profile-picture', src: '/app/views/design/icons/defaultAvatar.png', style: {width: '150px', height: '150px'}}, null),
					cel('p', {key: 'name'}, person.name),
					cel('button', {key: 'action-btn', onClick: this.handleAction}, buttonValue)
				]);
				return card;
			});
		} else return cel('p', {key:'notfoundmsg'}, 'Cannot find anyone.');
	}
}

ReactDOM.render(
	cel(People, null, null),
	document.getElementsByClassName('people-main')[0]
);

/* --- UI --- 
Switch Tabs */

function switchTabs(tab) {
	var tabs = document.getElementsByClassName("tab");
	var tabButtons = document.getElementsByClassName("tab-button");
	var stateObj;
	switch (tab) {
		case 0 :
			stateObj = { page: "search" };
			window.history.pushState(stateObj, "search", "/app/people/?page=search");
			
		break;
		case 1 :
			stateObj = { page: "known" };
			window.history.pushState(stateObj, "known", "/app/people/?page=known");
			loadKnownPeople();
	}
	for (var i = 0; i < tabs.length; i++) {
		if (i == tab) {
			tabs[i].style.display = "";
			tabButtons[i].style.background = "#32d8ca";
		} else {
			tabs[i].style.display = "none";
			tabButtons[i].style.background = "";
		}
	}
}