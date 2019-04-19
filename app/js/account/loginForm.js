const cel = React.createElement;

function submit(cr) {
	if (cr.email != null && cr.password != null) {
		submitLogin(cr, function(state) {finishLogin(state)});
	}
}

function finishLogin(state) {
	if (state == true) {
		window.location.replace("/app/show");
	} else {
		document.getElementById('res').innerHTML = 'Incorrect email or password.';
	} 
}

class LoginForm extends React.Component {
	constructor() {
		super();
		this.state = {email: '', password: ''};
		this.handleEmailChange = this.handleEmailChange.bind(this);
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	handleEmailChange(e) {
		this.setState({email: e.currentTarget.value});
	}
	
	handlePswdChange(e) {
		this.setState({password: e.currentTarget.value});
	}
	
	handleSubmit(e) {
		submit(this.state);
		e.preventDefault();
	}
	
	render() {
		return cel('form', {onSubmit: this.handleSubmit}, [
			cel('input', {name: 'email', type: 'email', onChange: this.handleEmailChange, placeholder: 'Email', required: true}, null),
			cel('input', {name: 'password', type: 'password', onChange: this.handlePswdChange, placeholder: 'Password', required: true}, null),
			cel('input', {type: 'submit', value: 'Login'}, null),
			cel('a', {onClick: this.handleForgotPswd, href: '#', style: {marginLeft: '5px'}}, 'Forgot my password')
		]);
	}
}

ReactDOM.render(
	cel(LoginForm, null, null),
	document.getElementsByClassName('reg-form')[0]
);