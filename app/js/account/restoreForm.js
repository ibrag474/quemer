const cel = React.createElement;

class restoreForm extends React.Component {
	constructor() {
		super();
		this.state = {code: '', password: '', repassword: '', btnStatus: false, message: 'Code sent to your email address.'};
		this.handleCodeChange = this.handleCodeChange.bind(this);
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleRePswdChange = this.handlePswdChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	handleCodeChange(e) {
		this.setState({code: e.currentTarget.value});
	}
	
	handlePswdChange(e) {
		this.setState({password: e.currentTarget.value});
	}
	
	handleRePswdChange(e) {
		this.setState({repassword: e.currentTarget.value});
	}
	
	handleSubmit(e) {
		const toSend = {code: this.state.code, password: this.state.password, repassword: this.state.repassword};
		submitPswdReset(this, toSend.code, toSend.password, toSend.repassword, (context, status, result) => {
			if (status == 200) {
				context.setState({btnStatus: true, message: cel('a', {href: '/account/login', key: 'loginA'}, result.message + ' Now you can login.')});
				document.getElementById("submit").style = "background:#a0a0a0; color: #666666; transition: 0.5s";
			} else alert(result.message + " " + result.exception); 
		});
		e.preventDefault();
	}
	
	render() {
		const btnStatus = this.state.btnStatus;
		return cel('form', {key: 'res-form', onSubmit: this.handleSubmit}, [
			cel('p', {key: 'messages', id: 'res'}, this.state.message),
			cel('input', {type: 'text', onChange: this.handleCodeChange, placeholder: 'Code', required: true}, null),
			cel('input', {type: 'password', onChange: this.handlePswdChange, placeholder: 'New password', required: true}, null),
			cel('input', {type: 'password', onChange: this.handleRePswdChange, placeholder: 'Retype new password', required: true}, null),
			cel('input', {id: 'submit', type: 'submit', value: 'Submit', disabled: btnStatus}, null),
		]);
	}
}

ReactDOM.render(
	cel(restoreForm, null, null),
	document.getElementsByClassName('res-form')[0]
);