const cel = React.createElement;

class Profile extends React.Component {
	constructor() {
		super();
		this.state = {profile: ''};
	}
	
	componentDidMount() {
		loadMe(this, (context, status, result) => {
			if (status == 200) {
				context.setState({profile: result});
			}
		});
	}
	
	render() {
		const profile = this.state.profile;
		return [
			cel(Header, {key: 'header'}, null),
			cel('div', {key: 'profile-div', className: 'profile'}, [
				cel('img', {key: 'circularIMG', className: 'circularIMG', src: '/app/views/design/icons/defaultAvatar.png', del: 'default profile picture'}, null),
				cel('div', {key: 'profile-name', className: 'profile-name'}, cel('div', {key: 'row', className: 'row'}, [
					cel('div', {key: 'name-col', className: 'col'}, cel('p', {key: 'name-p'}, profile.name)),
					cel('div', {key: 'surname-col', className: 'col'}, cel('p', {key: 'surname-p'}, 'No surname'))
				])),
			]),
			cel('div', {key: 'changepswd-div', className: 'profile-data'}, cel(ChangePasswdForm, null, null)),
			cel('div', {key: 'accInfo-div', className: 'profile-data'}, cel(AccInfo, {profile: profile}, null))
		];	
	}
}

class ChangePasswdForm extends React.Component {
	constructor() {
		super();
		this.state = {password: '', newpassword: '', newrepassword: '', btnStatus: false};
		this.handlePassword = this.handlePassword.bind(this);
		this.handleNewPassword = this.handleNewPassword.bind(this);
		this.handleNewRePassword = this.handleNewRePassword.bind(this);
		this.changePassword = this.changePassword.bind(this);
	}
	
	handlePassword(e) {
		const password = e.currentTarget.value;
		this.setState({password: password});
	}
	
	handleNewPassword(e) {
		const newPassword = e.currentTarget.value;
		this.setState({newpassword: newPassword});
	}
	
	handleNewRePassword(e) {
		const newRePassword = e.currentTarget.value;
		this.setState({newrepassword: newRePassword});
	}
	
	changePassword(e) {
		const pswd = this.state.password;
		const newpswd = this.state.newpassword;
		const newrepswd = this.state.newrepassword;
		if (newpswd === newrepswd) {
			changePassword(this, [pswd, newpswd], (context, status, result) => {
				if (status == 200) {
					context.setState({btnStatus: true});
					alert(result.message);
				} else alert(result.message + ' ' + result.exception);
			});
		}
		e.preventDefault();
	}
	
	render() {
		const pswdData = checkPswd(this.state.newpassword, this.state.newrepassword);
		const btnStatus = this.state.btnStatus;
		return cel('form' , {key: 'ChangePasswdForm', className: 'editAccForm', onSubmit: this.changePassword}, [
			cel('h4', {key: 'label'}, 'Change password'),
			cel('input', {key: 'paswd-input', type: 'password', placeholder: 'Current password', onChange: this.handlePassword}, null),
			cel('input', {key: 'newPaswd-input', type: 'password', placeholder: 'New password', onChange: this.handleNewPassword}, null),
			cel('input', {key: 'newRePaswd-input', type: 'password', placeholder: 'Retype new password', onChange: this.handleNewRePassword}, null),
			cel('input', {key: 'changePaswd-button', type: 'submit', value: 'Change', disabled: btnStatus}, null),
			cel('p', {key: 'pswdInfoText', className: 'pswdInfo', style: {color: pswdData.color}}, pswdData.text),
		]);	
	}
}

function checkPswd(pswd, repswd) {
	let data = {};
	if (pswd.length < 8) {
		if (pswd.length > 0) {
			data.text = 'Password is too short, ' + (8 - pswd.length) + ' characters left';
			data.color = 'red';
		} else data.text = '';
	} else if (repswd.length == 0){
		data.text = 'Appropriate password';
		data.color = 'green';
	} else {
		if (repswd === pswd) {
			data.text = 'Passwords match';
			data.color = 'green';
		} else {
			data.text = 'Passwords do not match!';
			data.color = 'red';
		}
	}
	return data;
}

class AccInfo extends React.Component {
	constructor(props) {
		super(props);
		this.state = {profile: this.props.profile};
	}
	
	componentDidUpdate() {
		if (this.props.profile != this.state.profile) {
			this.setState({profile: this.props.profile});
		}
	}
	
	render() {
		const profile = this.state.profile;
		const accStatus = profile.activated == 1 ? 'Activated' : 'Not activated';
		return [
			cel('h4', {key: 'label'}, 'Account details'),
			cel('p', {key: 'username-p'}, 'Username: ' + profile.name),
			cel('p', {key: 'email-p'}, 'Email: ' + profile.email),
			cel('p', {key: 'status-p'}, 'Account status: ' + accStatus),
		];
			
	}
}

ReactDOM.render(
	cel(Profile, null, null),
	document.getElementsByClassName('container')[0]
);