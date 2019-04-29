const cel = React.createElement;

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

function checkPswd(pswd, repswd) {
	let data = {};
	if (pswd.length < 8) {
		data.text = 'Password is too short, ' + (8 - pswd.length) + ' characters left.';
		data.color = 'red';
	} else if (repswd.length == 0){
		data.text = 'Appropriate password.';
		data.color = 'green';
	} else {
		if (repswd === pswd) {
			data.text = 'Passwords match.';
			data.color = 'green';
		} else {
			data.text = 'Passwords do not match!';
			data.color = 'red';
		}
	}
	return data;
}

class PasswordInput extends React.Component {
	constructor(props) {
		super(props);
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleRePswdChange = this.handleRePswdChange.bind(this);
	}
	
	handlePswdChange(event) {
		this.props.onPasswordChange(event.currentTarget.value);
	}
	
	handleRePswdChange(event) {
		this.props.onRePasswordChange(event.currentTarget.value);
	}
	
	render() {
		const pswd = this.props.pswd;
		const repswd = this.props.repswd;
		const pswdData = checkPswd(pswd, repswd);
		const inputStyle = {margin: '0px 0px'};
		return [
			cel('input', {style: inputStyle, id: 'pswd', name: 'password', type: 'password', placeholder: 'Password', onChange: this.handlePswdChange, value: pswd, required: true}, null),
			cel('p', {className: 'pswdInfo', style: {color: pswdData.color}}, pswdData.text),
			cel('input', {id: 'repswd', name: 'rePassword', type: 'password', placeholder: 'Re-enter your password', onChange: this.handleRePswdChange, required: true}, null),
		];
	}
}

class RegisterForm extends React.Component {
	constructor() {
		super();
		this.state = {password: '', rePassword: '', message: '', btnStatus: false};
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleRePswdChange = this.handleRePswdChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	handlePswdChange(pswd) {
		this.setState({password: pswd});
	}
					  
	handleRePswdChange(repswd) {
		this.setState({rePassword: repswd});
	}
	
	handleSubmit(e) {
		const data = {
			email: e.currentTarget.email.value,
			name: e.currentTarget.name.value,
			surname: e.currentTarget.surname.value,
			password: e.currentTarget.password.value,
			rePassword: e.currentTarget.rePassword.value
		};
	
		if (data.rePassword === data.password && data.password.length >= 8) {
			data.name = data.name.capitalize();
			data.surname = data.surname.capitalize();
			submitSignOn(this, data, (context, status, result) => {
				if (status == 200) {
					context.setState({btnStatus: true, message: cel('a', {href: '/account/login', key: 'LoginBtn'}, 'Account is registered succesfully. Activation link sent to your email. Click here to login')});
					document.getElementById("submit").style = "background:#a0a0a0; color: #666666; transition: 0.5s";
				} else context.setState({message: result.message + ' ' + result.message});
			});
		}
		e.preventDefault();
	}
	
	render() {
		const btnStat = this.state.btnStatus;
		return cel('form', {onSubmit: this.handleSubmit}, [
			cel('p', {key: 'messages', id: 'res'}, this.state.message),
			cel('input', {name: 'email', type: 'email', placeholder: 'Email', required: true}, null),
			cel('input', {name: 'name', type: 'text', placeholder: 'Name', required: true}, null),
			cel('input', {name: 'surname', type: 'text', placeholder: 'Surname', required: true}, null),
			cel(PasswordInput, {pswd: this.state.password, repswd: this.state.rePassword, onPasswordChange: this.handlePswdChange, onRePasswordChange: this.handleRePswdChange}, null),
			cel('input', {id: 'submit', type: 'submit', disabled: btnStat, value: 'Register'}, null),
			cel('h5', null, ['By creating an account you agree to our ',
				cel('a', {href: '/app/views/legal/qtou.pdf'}, 'Terms Of Use'), ' and ',
				cel('a', {href: '/app/views/legal/qpp.pdf'}, 'Privacy Policy')
			])
		]);
	}
}

ReactDOM.render(
	cel(RegisterForm, null, null),
	document.getElementsByClassName('reg-form')[0]
);