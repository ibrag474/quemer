const cel = React.createElement;

class LoginForm extends React.Component {
	constructor() {
		super();
		this.state = {email: '', password: '', message: ''};
		this.handleEmailChange = this.handleEmailChange.bind(this);
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleForgotPswd = this.handleForgotPswd.bind(this);
		this.resendActCode = this.resendActCode.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	handleEmailChange(e) {
		this.setState({email: e.currentTarget.value});
	}
	
	handlePswdChange(e) {
		this.setState({password: e.currentTarget.value});
	}
	
	handleForgotPswd(e) {
		const email = this.state.email;
		if (email.length > 3) {
			forgotPswd(this, email, (context, status, result) => {
				if (status == 200) {
					window.location.replace("/account/restore");
				} else {
					document.getElementById('res').innerHTML = result.message + result.exception;
				}
			});
		} else alert('Please fill in your email address.');
		
		e.preventDefault();
	}
	
	resendActCode(e) {
		const email = this.state.email;
		if (email.length > 3) {
			submitResendActCode(this, email, (context, status, result) => {
				if (status == 200) {
					document.getElementById('res').innerHTML = result.message;
				} else {
					document.getElementById('res').innerHTML = result.message + result.exception;
				}
			});
		} else alert('Please fill in your email address.');
		e.preventDefault();
	}
	
	handleSubmit(e) {
		const cr = {email: this.state.email, password: this.state.password};
		if (cr.email != null && cr.password != null) {
			submitLogin(this, cr, (context, status, result) => {
				if (status == 200) {
					window.location.replace("/app/show");
				} else if (status == 422) {
					context.setState({message: cel('a', {key:'resendactcodeA', href:'#', onClick: this.resendActCode}, result.message + ' ' + result.exception + '. Click to here to resend activation code to your email address.')});
				} else {
					//document.getElementById('res').innerHTML = result.message + result.exception;
					context.setState({message: result.message + " " + result.exception});
				}
			});
		}
		e.preventDefault();
	}
	
	render() {
		return cel('form', {onSubmit: this.handleSubmit}, [
			cel('p', {key: 'messages', id: 'res'}, this.state.message),
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