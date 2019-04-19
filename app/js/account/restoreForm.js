const cel = React.createElement;

class restoreForm extends React.Component {
	constructor() {
		super();
		this.state = {code: '', password: '', repassword: ''};
		this.handleCodeChange = this.handleCodeChange.bind(this);
		this.handlePswdChange = this.handlePswdChange.bind(this);
		this.handleRePswdChange = this.handlePswdChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	handleCodeChange(e) {
		
	}
	
	handlePswdChange(e) {
		this.setState({password: e.currentTarget.value});
	}
	
	handleRePswdChange(e) {
		this.setState({password: e.currentTarget.value});
	}
	
	handleSubmit(e) {
		//submit(this.state);
		e.preventDefault();
	}
	
	render() {
		return cel('form', {key: 'res-form', onSubmit: this.handleSubmit}, [
			cel('input', {type: 'text', onChange: this.handleCodeChange, placeholder: 'Code', required: true}, null),
			cel('input', {type: 'password', onChange: this.handlePswdChange, placeholder: 'New password', required: true}, null),
			cel('input', {type: 'password', onChange: this.handleRePswdChange, placeholder: 'Retype new password', required: true}, null),
			cel('input', {type: 'submit', value: 'Submit'}, null),
		]);
	}
}

ReactDOM.render(
	cel(restoreForm, null, null),
	document.getElementsByClassName('res-form')[0]
);