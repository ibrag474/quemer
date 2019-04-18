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

function submit(e) {
	const data = {
		email: e.target.email.value,
		name: e.target.name.value,
		surname: e.target.surname.value,
		password: e.target.password.value,
		rePassword: e.target.rePassword.value
	};
	
	if (data.rePassword === data.password && data.password.length >= 8) {
		data.name = data.name.capitalize();
		data.surname = data.surname.capitalize();
		submitSignOn(data);
	}
	
}

class PasswordInput extends React.Component {
	constructor(props) {
		super(props);
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleRePswdChange = this.handleRePswdChange.bind(this);
	}
	
	handlePswdChange(event) {
		this.props.onPasswordChange(event.target.value);
	}
	
	handleRePswdChange(event) {
		this.props.onRePasswordChange(event.target.value);
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
		this.state = {password: '', rePassword: ''};
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
	
	handleSubmit(event) {
		submit(event);
		event.preventDefault();
	}
	
	render() {
		return cel('form', {onSubmit: this.handleSubmit}, [
			cel('input', {name: 'email', type: 'email', placeholder: 'Email', required: true}, null),
			cel('input', {name: 'name', type: 'text', placeholder: 'Name', required: true}, null),
			cel('input', {name: 'surname', type: 'text', placeholder: 'Surname', required: true}, null),
			cel(PasswordInput, {pswd: this.state.password, repswd: this.state.rePassword, onPasswordChange: this.handlePswdChange, onRePasswordChange: this.handleRePswdChange}, null),
			cel('input', {id: 'submit', type: 'submit', value: 'Register'}, null),
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