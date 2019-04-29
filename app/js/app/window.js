class CheckBox extends React.Component {
	constructor(props) {
		super(props);
		this.handleCheckBox = this.handleCheckBox.bind(this);
	}
	
	handleCheckBox(e) {
		this.props.onCheckClick(e.currentTarget);
	}
	
	render() {
		if (this.props.data == 1)
			return cel('input', {key: 'checkbox', type: 'checkbox', onChange: this.handleCheckBox, name: 'done', 'data-index': this.props.index, defaultChecked: 1}, null);
		else return cel('input', {key: 'checkbox', type: 'checkbox', onChange: this.handleCheckBox, name: 'done', 'data-index': this.props.index, defaultChecked: 0}, null);
	}
}

class TaskInputs extends React.Component {
	constructor(props) {
		super(props);
		this.state = ({content: this.props.data.content});
		this.handleTaskEdit = this.handleTaskEdit.bind(this);
		this.handleTaskDelete = this.handleTaskDelete.bind(this);
		this.handleCheckBox = this.handleCheckBox.bind(this);
	}
	
	handleTaskEdit(e) {
		let value = e.currentTarget.value;
		let index = e.currentTarget.attributes['data-index'].value;
		let stateCopy = this.state.content;
		if (value !== stateCopy[index].task) {
			stateCopy[index].task = value;
			this.setState({content: stateCopy});
			this.props.onTasksChange(this.state.content);
		}
	}
	
	handleTaskDelete(e) {
		let value = e.currentTarget.value;
		let index = e.currentTarget.parentElement.children[1].attributes['data-index'].value;
		let stateCopy = this.state.content;
		stateCopy.splice(index, 1);
		this.setState({content: stateCopy});
		this.props.onTasksChange(this.state.content);
	}
	
	handleCheckBox(e) {
		let checked = e.checked;
		let index = e.attributes['data-index'].value;
		let stateCopy = this.state.content;
		if (checked !== stateCopy[index].done) {
			if (checked == false)
				stateCopy[index].done = '0';
			else stateCopy[index].done = '1';
			this.setState({content: stateCopy});
			this.props.onTasksChange(this.state.content);
		}
	}
	
	render() {
		const tasks = this.state.content.map((task, index) => {
			return cel('div', {key: 'sp-window-taskinp'+ task.todoId, className: 'sp-window-taskinp'}, [
				cel(CheckBox, {key: 'checkBox', onCheckClick: this.handleCheckBox, data: task.done, index: index}, null),
				cel('input', {key: 'addedInputArea', id: 'taskInput', type: 'text', name: 'addedInputArea', placeholder: 'Your task', 'data-index': index, onChange: this.handleTaskEdit, defaultValue: task.task}),
				cel('button', {key: 'delete-btn', type: 'button', onClick: this.handleTaskDelete}, 
				   cel('img', {key: 'delete-btn-icon', src: '/app/views/design/icons/delete.png', alt: 'delete icon'}, null)),
			]);
		});
		return cel('div', {key: 'sp-window-notetext', id: 'taskarea', className: 'sp-window-notetext'}, tasks);
	}
}

class SelectShareTo extends React.Component {
	constructor() {
		super();
		this.state = {groups: []};
		this.handleOptionPick = this.handleOptionPick.bind(this);
	}
	
	componentDidMount() {
		this.props.onOptionPick('0');
		loadGroups(this, (context, status, groups) => {
			if (status == 200) {
				context.setState({groups: groups});
			}
		});
	}
	
	handleOptionPick(e) {
		this.props.onOptionPick(e.currentTarget.value);
	} 
	
	render() {
		let options = this.state.groups.map(group => {
			return cel('option', {key: 'op' +  group.groupID, onClick: this.handleOptionPick, value: group.groupID}, group.groupName);
		});
		options.unshift(cel('option', {key: 'op0', value: '0'}, 'Do not share'));
		return options;
	}
}

class AddForm extends React.Component {
	constructor(props) {
		super(props);
		if (this.props.type == 1) this.state = {note: {type: '1',title: '', content: [{todoId: '', task: '', done: '0'}]}};
		else this.state = {type: this.props.type, note: {type: '0', title: '', content: ''}};
		this.handleTitle = this.handleTitle.bind(this);
		this.handleOptionPick = this.handleOptionPick.bind(this);
		this.handleNote = this.handleNote.bind(this);
		this.handleTasks = this.handleTasks.bind(this);
		this.handleAddTask = this.handleAddTask.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	handleTitle(e) {
		let stateCopy = this.state.note;
		stateCopy.title = e.currentTarget.value;
		this.setState({note: stateCopy});
	}
	
	handleOptionPick(groupId) {
		let stateCopy = this.state.note;
		stateCopy.shareTo = groupId;
		this.setState({note: stateCopy});
	}
	
	handleNote(e) {
		let stateCopy = this.state.note;
		stateCopy.content = e.currentTarget.value;
		this.setState({note: stateCopy});
	}
	
	handleTasks(tasks) {
		let stateCopy = this.state.note;
		stateCopy.content = tasks;
		this.setState({note: stateCopy});
	}
	
	handleAddTask() {
		let stateCopy = this.state.note;
		stateCopy.content.push({todoId: '' + stateCopy.content.length + '', task: '', done: '0'});
		this.setState({note: stateCopy});
	}
	
	handleSubmit(e) {
		if (this.state.note.content.length != 0 || this.state.note.content.length != 0)
			this.props.onSubmitClick(this.state.note);
		else e.currentTarget[1].style.borderColor = 'red';
		e.preventDefault();
	}
	
	componentDidMount() {
		center();
	}
	
	render() {
		const data = this.state.note;
		const form = this.state.type == 0 ? cel('textarea', {key: 'sp-window-notetext', className: 'sp-window-notetext', name: 'notes', placeholder: 'Notes', onChange: this.handleNote}, null) : [cel(TaskInputs, {key: 'taskinputs', onTasksChange: this.handleTasks, data: data}, null), cel('input', {key:'addMoreTasks', type: 'button', style: {float: 'left'}, onClick: this.handleAddTask, value: 'Add one more'}, null)];
		return cel('form', {key: 'noteForm', name: 'noteForm', onSubmit: this.handleSubmit}, [
			cel('input', {key: 'sp-window-titleInput', id: 'sp-window-titleInput', type: 'text', name: 'title', placeholder: 'Title', onChange: this.handleTitle}, null),
			form,
			cel('select', {key: 'sp-window-shareTo', name: 'shareTo'}, cel(SelectShareTo, {onOptionPick: this.handleOptionPick}, null)),
			cel('input', {key: 'submit-btn', id: 'submit', type: 'submit', value: 'Save'}, null),
		]);
	}
}

class EditForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {note: this.props.data};
		this.handleTitle = this.handleTitle.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleNote = this.handleNote.bind(this);
		this.handleTasks = this.handleTasks.bind(this);
		this.handleAddTask = this.handleAddTask.bind(this);
	}
	
	componentDidMount() {
		center();
	}
	
	handleTitle(e) {
		let stateCopy = this.state.note;
		stateCopy.title = e.currentTarget.value;
		this.setState({note: stateCopy});
	}
	
	handleNote(e) {
		let stateCopy = this.state.note;
		stateCopy.content = e.currentTarget.value;
		this.setState({note: stateCopy});
	}
	
	handleTasks(content) {
		let stateCopy = this.state.note;
		stateCopy.content = content;
		this.setState({note: stateCopy});
	}
	
	handleAddTask() {
		let stateCopy = this.state.note;
		stateCopy.content.push({todoId: '' + stateCopy.content.length + '', task: '', done: '0'});
		this.setState({note: stateCopy});
	}
	
	handleSubmit(e) {
		if (this.state.note.content.length != 0 || this.state.note.content.length != 0) {
			e.currentTarget[1].style.borderColor = '#ededed';
			this.props.onSubmitClick(this.state.note);
		}
		else e.currentTarget[1].style.borderColor = 'red';
		e.preventDefault();
	}

	render() {
		const data = this.state.note;
		const form = data.type == 0 || data.type == 2 ? cel('textarea', {key: 'sp-window-notetext', className: 'sp-window-notetext', name: 'notes', placeholder: 'Notes', onChange: this.handleNote, defaultValue: data.content}, null) : [cel(TaskInputs, {key: 'taskinputs', onTasksChange: this.handleTasks, data: data}, null), cel('input', {key:'addMoreTasks', type: 'button', style: {float: 'left'}, onClick: this.handleAddTask, value: 'Add one more'}, null)];
		return cel('form', {key: 'noteForm', name: 'noteForm', onSubmit: this.handleSubmit}, [
			cel('input', {key: 'sp-window-titleInput', id: 'sp-window-titleInput', type: 'text', name: 'title', placeholder: 'Title', onChange: this.handleTitle, defaultValue: data.title}, null),
			form,
			cel('input', {key: 'submit-btn', id: 'submit', type: 'submit', value: 'Save'}, null),
		]);
	}
	
}

class ShareForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {note: {}, groupName: ''};
		this.handleTitle = this.handleTitle.bind(this);
		this.handleShare = this.handleShare.bind(this);
	}
	
	componentDidMount() {
		this.setState({note: this.props.data});
		center();
	}
	
	handleTitle(e) {
		this.setState({groupName: e.currentTarget.value});
	}
	
	handleShare(e) {
		this.props.onShareClick(this.state.note, this.state.groupName);
		e.preventDefault();
	}

	render() {
		return cel('form', {key: 'shareForm', name: 'shareForm', onSubmit: this.handleShare}, [
			cel('input', {key: 'sp-window-titleInput', id: 'sp-window-titleInput', type: 'text', name: 'title', placeholder: 'Group name', onChange: this.handleTitle}, null),
			cel('div', {key: 'sp-window-notetext', id: 'taskarea', className: 'sp-window-notetext'}, null),
			cel('input', {key: 'submit-btn', id: 'submit', type: 'submit', value: 'Save'}, null),
		]);
	}
	
}

class DeleteForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {note: {}};
		this.handleAnswer = this.handleAnswer.bind(this);
		this.handleCancel = this.handleCancel.bind(this);
	}
	
	componentDidMount() {
		this.setState({note: this.props.data});
	}
	
	handleAnswer(e) {
		this.props.onDeleteClick(this.state.note);
		e.preventDefault();
	}

	handleCancel() {
		this.props.onCancelClick();
	}
	
	render() {
		const content = this.state.note.type == 0 || this.state.note.type == 2 ? 'note' : 'task list';
		return cel('div', {key: 'sp-window-notetext', id: 'taskarea', className: 'sp-window-delete'}, [
			cel('p', {key: 'SureQuestion'}, 'Are you sure you want to delete this ' + content + '?'),
			cel('input', {key: 'delete-btn', id: 'submit', type: 'button', onClick: this.handleAnswer, value: 'Delete'}, null),
			cel('input', {key: 'cancel-btn', id: 'delete', type: 'button', onClick: this.handleCancel, value: 'Cancel'}, null)
		]);
	}
	
}

class CreateGroupForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {groupName: '', groupMembers: [], friends: []};
		this.handleTitle = this.handleTitle.bind(this);
		this.handleActionButton = this.handleActionButton.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	componentDidMount() {
		loadFriends(this, function(context, status, result) {
			if (status == 200) {
				context.setState({friends: result});
			}
		});
		center();
	}
	
	componentDidUpdate() {
		center();
	}
	
	handleTitle(e) {
		this.setState({groupName: e.currentTarget.value});
	}
	
	handleActionButton(e) {
		const userid = e.currentTarget.parentElement.attributes['data-userid'].value;
		let stateCopy = this.state.groupMembers;
		if (stateCopy.indexOf(userid) == -1) {
			stateCopy.push(userid);
			e.currentTarget.innerHTML = 'Cancel';
		} else {
			stateCopy.splice(stateCopy.indexOf(userid), 1);
			e.currentTarget.innerHTML = 'Invite';
		}
		this.setState({groupMembers: stateCopy});
	}
	
	handleSubmit(e) {
		let group = {groupName: this.state.groupName, groupMembers: this.state.groupMembers};
		this.props.onSubmitClick(group);
		e.preventDefault();
	}
	
	render() {
		const friendsList = this.state.friends.map(friend => {
			const friendID = friend.userid1 == friend.myid ? friend.userid2 : friend.userid1;
			return cel('div', {key: 'sp-window-taskinp' + friendID, className: 'sp-window-taskinp'},
				cel('div', {key: 'friend' + friendID, 'data-userid': friendID}, [
				   	cel('span', {key: 'friend' + friendID + 'name', name: 'addedInputArea'}, friend.name),
					cel('button', {key: 'delete-btn', type: 'button', style: {float: 'right'}, onClick: this.handleActionButton}, 'Invite')])
				);
		});
		return cel('form', {key: 'noteForm', name: 'noteForm', onSubmit: this.handleSubmit}, [
			cel('input', {key: 'sp-window-titleInput', id: 'sp-window-titleInput', type: 'text', name: 'title', placeholder: 'Title', onChange: this.handleTitle}, null),
			cel('div', {key: 'sp-window-notetext', id: 'taskarea', className: 'sp-window-notetext'}, friendsList),
			cel('input', {key: 'submit-btn', id: 'submit', type: 'submit', value: 'Save'}, null)
		]);
	}
}

class EditGroupForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {group: this.props.group, groupAdmin: this.props.group.adminID, groupName: this.props.group.groupName, groupMembers: this.props.group.members, friends: []};
		this.handleTitle = this.handleTitle.bind(this);
		this.handleActionButton = this.handleActionButton.bind(this);
		this.removeUser = this.removeUser.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}
	
	componentDidMount() {
		loadFriends(this, function(context, status, result) {
			if (status == 200) {
				context.setState({friends: result});
			}
		});
		center();
	}
	
	componentDidUpdate() {
		center();
	}
	
	handleTitle(e) {
		this.setState({groupName: e.currentTarget.value});
	}
	
	handleActionButton(e) {
		if (this.state.group.myid == this.state.group.adminID) {
			const userid = e.currentTarget.parentElement.attributes['data-userid'].value;
			const userName = e.currentTarget.parentElement.children[0].textContent;
			let stateCopy = this.state.groupMembers;
			if (stateCopy.indexOf(userid) == -1) {
				stateCopy.push({userID: userid, name: userName});
				e.currentTarget.innerHTML = 'Cancel';
			} else {
				stateCopy.splice(stateCopy.indexOf(userid), 1);
				e.currentTarget.innerHTML = 'Invite';
			}
			this.setState({groupMembers: stateCopy});
		} else alert("Only group administrator have right to edit it!");
	}
	
	removeUser(e) {
		if (this.state.group.myid == this.state.group.adminID) {
			const userid = e.currentTarget.parentElement.attributes['data-userid'].value;
			let stateCopy = this.state.groupMembers;
			stateCopy = stateCopy.filter(member => member.userID != userid);
			this.setState({groupMembers: stateCopy});
		} else alert("Only group administrator have right to edit it!");
	}
	
	handleSubmit(e) {
		if (this.state.group.myid == this.state.group.adminID) {
			const memID = this.state.groupMembers.map(member => member.userID);
			let group = {groupID: this.state.group.groupID, groupName: this.state.groupName, groupMembers: memID};
			e.preventDefault();
			this.props.onSubmitClick(group);
		} else {
			e.preventDefault();
			alert("Only group administrator have right to edit it!");
		}
	}
	
	render() {
		let friends = this.state.friends;
		const groupAdmin = this.state.groupAdmin;
		let inGroup = [];
		const memberList = this.state.groupMembers.map(member => {
			inGroup.push(member.userID);
			if (groupAdmin == member.userID) {
				return cel('div', {key: 'members' + member.userID, className: 'sp-window-taskinp'},
					cel('div', {key: 'member' + member.userID, 'data-userid': member.userID}, [
						cel('span', {key: 'member' + member.userID + 'name', name: 'addedInputArea'}, member.name + '(admin)'),
						cel('button', {key: 'expel-btn', type: 'button', style: {float: 'right'}, disabled: true}, 'Expel')])
					);
			} else {
				return cel('div', {key: 'members' + member.userID, className: 'sp-window-taskinp'},
					cel('div', {key: 'member' + member.userID, 'data-userid': member.userID}, [
						cel('span', {key: 'member' + member.userID + 'name', name: 'addedInputArea'}, member.name),
						cel('button', {key: 'expel-btn', type: 'button', style: {float: 'right'}, onClick: this.removeUser}, 'Expel')])
					);
			}
		});
		friends = friends.filter(friend => !inGroup.includes(friend.userid1) || !inGroup.includes(friend.userid2));
		const friendsList = friends.map(friend => {
			const friendID = friend.userid1 == friend.myid ? friend.userid2 : friend.userid1;
			return cel('div', {key: 'friends' + friendID, className: 'sp-window-taskinp'},
				cel('div', {key: 'friend' + friendID, 'data-userid': friendID}, [
				   	cel('span', {key: 'friend' + friendID + 'name', name: 'addedInputArea'}, friend.name),
					cel('button', {key: 'delete-btn', type: 'button', style: {float: 'right'}, onClick: this.handleActionButton}, 'Invite')])
				);
		});
		return cel('form', {key: 'noteForm', name: 'noteForm', onSubmit: this.handleSubmit}, [
			cel('input', {key: 'sp-window-titleInput', id: 'sp-window-titleInput', type: 'text', name: 'title', placeholder: 'Title', defaultValue: this.state.group.groupName, onChange: this.handleTitle}, null),
			cel('div', {key: 'sp-window-notetext', id: 'taskarea', className: 'sp-window-notetext'}, [memberList, friendsList]),
			cel('input', {key: 'submit-btn', id: 'submit', type: 'submit', value: 'Save'}, null)
		]);
	}
}

class DeleteGroupForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {group: this.props.group, groupAdmin: this.props.group.adminID, groupName: this.props.group.groupName, groupMembers: this.props.group.members};
		this.handleAnswer = this.handleAnswer.bind(this);
		this.handleCancel = this.handleCancel.bind(this);
	}
	
	componentDidMount() {
		
	}
	
	handleAnswer(e) {
		this.props.onDeleteClick(this.state.group);
		e.preventDefault();
	}

	handleCancel() {
		this.props.onCancelClick();
	}
	
	render() {
		const actionName = this.state.groupAdmin == this.state.group.myid ? 'delete' : 'leave';
		return cel('div', {key: 'sp-window-notetext', id: 'taskarea', className: 'sp-window-delete'}, [
			cel('p', {key: 'SureQuestion'}, 'Are you sure that you want to ' + actionName + ' ' + this.state.groupName + '?'),
			cel('input', {key: 'delete-btn', id: 'submit', type: 'button', onClick: this.handleAnswer, value: actionName.charAt(0).toUpperCase() + actionName.slice(1)}, null),
			cel('input', {key: 'cancel-btn', id: 'delete', type: 'button', onClick: this.handleCancel, value: 'Cancel'}, null)
		]);
	}
}

function renderForm(context, action, data) {
	if (action == 'addNote') {
		return cel(AddForm, {key: 'addForm', onSubmitClick: context.handleAddNote, type: 0});
	} else if (action == 'addTask') {
		return cel(AddForm, {key: 'addForm', onSubmitClick: context.handleAddNote, type: 1});
	} else if (action == 'createGroup') {
		return cel(CreateGroupForm, {key: 'createGroupForm', onSubmitClick: context.handleCreateGroup});
	} else if (action == 'editGroup') {
		return cel(EditGroupForm, {key: 'EditGroupForm', group: data, onSubmitClick: context.handleEditGroup});
	} else if (action == 'edit') {
		return cel(EditForm, {key: 'editForm', onSubmitClick: context.handleEdit, data: data}, null);
	} else if (action == 'share') {
		return cel(ShareForm, {key: 'ShareForm', onShareClick: context.handleShare, data: data}, null);
	} else if (action == 'delete') {
		return cel(DeleteForm, {key: 'DeleteForm', onDeleteClick: context.handleDelete, onCancelClick: context.closeWindow, data: data}, null);
	} else if (action == 'deleteGroup') {
		return cel(DeleteGroupForm, {key: 'DeleteGroupForm', group: data, onDeleteClick: context.handleDeleteGroup, onCancelClick: context.closeWindow});
	}
}

function windowTitle(action, data) {
	switch(action) {
		case 'addNote' :
			return 'Add note';
			break;
		case 'addTask' :
			return 'Add task';
			break;
		case 'createGroup' :
			return 'Create group';
			break;
		case 'edit' :
			if (data.type == 0 || data.type == 2)
				return 'Edit note';
			else return 'Edit task';
			break;
		case 'editGroup' :
			return 'Edit group';
			break;
		case 'share' :
			if (data.type == 0 || data.type == 2)
				return 'Share note';
			else return 'Share task';
			break;
		case 'delete' :
			if (data.type == 0 || data.type == 2)
				return 'Delete note';
			else return 'Delete task';
			break;
		case 'deleteGroup' :
			return 'Delete group';
			break;
	}
}

class Window extends React.Component {
	constructor(props) {
		super(props);
		this.state = {action: this.props.action, data: this.props.data};
		this.closeWindow = this.closeWindow.bind(this);
		this.handleEdit = this.handleEdit.bind(this);
		this.handleShare = this.handleShare.bind(this);
		this.handleDelete = this.handleDelete.bind(this);
		this.handleAddNote = this.handleAddNote.bind(this);
		this.handleAddTask = this.handleAddTask.bind(this);
		this.handleCreateGroup = this.handleCreateGroup.bind(this);
		this.handleEditGroup = this.handleEditGroup.bind(this);
		this.handleDeleteGroup = this.handleDeleteGroup.bind(this);
	}
	//note management
	handleAddNote(note) {
		this.props.onNoteAdd(note);
	}
	
	handleAddTask(note) {
		this.props.onNoteAdd(note);
	}
	
	handleEdit(note) {
		this.props.onNoteEdit(note);
	}
	
	handleShare(note, gn) {
		if (gn.length > 0 && note != null) {
			this.props.onNoteShare(note, gn);
		} else alert('Please specify group name.');
	}
	
	handleDelete(note) {
		this.props.onNoteDelete(note);
	}
	//group management
	handleCreateGroup(group) {
		this.props.onCreateGroup(group);
	}
	
	handleEditGroup(group) {
		this.props.onEditGroup(group);
	}
	
	handleDeleteGroup(group) {
		this.props.onDeleteGroup(group);
	}
	
	closeWindow() {
		this.props.onWindowClose();
	}
	
	render() {
		const action = {action: this.state.action, data: this.state.data};
		const window = cel('div', {key: 'sp-window', className: 'sp-window'}, [
			cel('p', {key: 'sp-window-title', id: 'sp-window-title'}, windowTitle(action.action, action.data)),
			cel('button', {key: 'windowCloseButton',onClick: this.closeWindow}, 'X'),
			renderForm(this, action.action, action.data)
		]);
		return ReactDOM.createPortal(
			window,
			document.getElementsByTagName('body')[0]
		);
	}
}