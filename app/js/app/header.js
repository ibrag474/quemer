class Header extends React.Component {
	constructor(props) {
		super(props);
		this.state = {accTabState: false};
		this.openSideBar = this.openSideBar.bind(this);
		this.myAccountTab = this.myAccountTab.bind(this);
	}
	
	openSideBar() {
		sidebardet();
	}
	
	myAccountTab(e) {
		if (this.state.accTabState == false)
			this.setState({accTabState: true});
		else this.setState({accTabState: false});
		e.preventDefault();
	}
	
	render() {
		const myAccountTab = this.state.accTabState == true ? cel(MyAccountTab, {key: 'myAcountTab'}, null) : null;
		return ReactDOM.createPortal(
			[
				cel('button', {key: 'menu-button', className: 'menu-button', onClick: this.openSideBar}, cel('img', {src: '/app/views/design/icons/menu-hamburger.png', className: 'menu-picture'}, null)),
				cel('a', {key: 'menu-quemer-logo', href: '/app/show'}, cel('p', {className: 'name'}, 'Quemer')),
				cel('div', {key: 'search-box', id: 'search-box', className: 'search-box'}, [
					cel('img', {key: 'search-icon', src: '/app/views/design/icons/search-icon.png', alt: 'search-icon'}, null),
					cel('input', {key: 'search-input', type: 'text', name: 'search-input', placeholder: 'Search'}, null),
				]),
				cel('div', {key: 'header-buttons-div', className: 'header-buttons-div'}, [
					cel('a', {key: 'friends-button', href: '/app/people'}, cel('img', {src: '/app/views/design/icons/friends.png', alt: 'friends icon'}, null)),
					cel('a', {key: 'my-profile-button', style: {marginLeft: '5px'}, href: '', onClick: this.myAccountTab}, cel('img', {className: 'circularIMG', src: '/app/views/design/icons/defaultAvatar.png', alt: 'default profile picture'}, null)),
				]),
				myAccountTab,
			],
			document.getElementsByClassName('header')[0]
		);
	}
}

/*
<div class="row"><div class="col-5">\
	<img class="circularIMG" src="/app/views/design/icons/defaultAvatar.png" del="default profile picture"></div>\
	<div class="col-7"><p>Name Surname</p> <a href="/app/profile">More</a><br>\
	<a href="/account/logout">Log out</a><div></div>
*/

class MyAccountTab extends React.Component {
	constructor(props) {
		super(props);
		this.state = {me: []};
		this.handleLogOut = this.handleLogOut.bind(this);
	}
	
	componentDidMount() {
		loadMe(this, (context, status, result) => {
			if (status == 200) {
				context.setState({me: result});
			}
		});
	}
	
	handleLogOut(e) {
		logOUT();
		e.preventDefault();
	}
	
	render() {
		return ReactDOM.createPortal(
			cel('div', {key: 'myAcountTab0', className: 'accAct'}, 
				cel('div', {key: 'myAcountTab-row', className: 'row'}, [
					cel('div', {key: 'myAcountTab-col5',className: 'col-5'}, 
						cel('img', {key: 'circularIMG', className: 'circularIMG', src: '/app/views/design/icons/defaultAvatar.png', del: 'default profile picture'}, null)),
					cel('div', {key: 'myAcountTab-col7', className: 'col-7'}, [
						cel('p', {key: 'myname',}, this.state.me.name + ' ' + this.state.me.surname),
						cel('a', {key: 'more-btn', href: '/app/profile'}, 'More'), cel('br', {key: 'br',}, null),
						cel('a', {key: 'logout-btn', href: '#', onClick: this.handleLogOut}, 'Log out'),
					]),
			 	])),
			document.getElementsByTagName('body')[0]
		);		
	}
}