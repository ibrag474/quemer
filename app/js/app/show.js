const cel = React.createElement;
String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

class NoteAction extends React.Component {
	constructor() {
		super();
		this.handleEditButton = this.handleEditButton.bind(this);
		this.handleShareButton = this.handleShareButton.bind(this);
		this.handleDeleteButton = this.handleDeleteButton.bind(this);
		this.state = {};
	}
	
	handleEditButton(e) {
		this.props.onActionClick(e.currentTarget, 'edit');
	}
	
	handleShareButton(e) {
		this.props.onActionClick(e.currentTarget, 'share');
	}
	
	handleDeleteButton(e) {
		this.props.onActionClick(e.currentTarget, 'delete');
	}
	
	render() {
		const shareBtn = this.props.type == 0 ? cel('button', {type: 'button', key: 'action2', onClick: this.handleShareButton}, 
				cel('img', {src: '/app/views/design/icons/share.png', alt: 'share icon'}, null)) : null;
		return cel('div', {className: 'actions'}, [
			cel('button', {type: 'button', key: 'action1', onClick: this.handleEditButton}, 
				cel('img', {src: '/app/views/design/icons/edit.png', alt: 'edit icon'}, null)),
			shareBtn,
			cel('button', {type: 'button', key: 'action3', onClick: this.handleDeleteButton}, 
				cel('img', {src: '/app/views/design/icons/delete.png', alt: 'delete icon'}, null)),
		]);
	}
}

class GroupAction extends React.Component {
	constructor() {
		super();
		this.handleEditButton = this.handleEditButton.bind(this);
		this.handleShareButton = this.handleShareButton.bind(this);
		this.handleDeleteButton = this.handleDeleteButton.bind(this);
		this.state = {};
	}
	
	handleEditButton(e) {
		this.props.onActionClick(e.currentTarget, 'editGroup');
	}
	
	handleShareButton(e) {
		this.props.onActionClick(e.currentTarget, 'share');
	}
	
	handleDeleteButton(e) {
		this.props.onActionClick(e.currentTarget, 'deleteGroup');
	}
	
	render() {
		return cel('div', {className: 'actions'}, [
			cel('button', {type: 'button', key: 'action1', onClick: this.handleEditButton}, 
				cel('img', {src: '/app/views/design/icons/edit.png', alt: 'edit icon'}, null)),
			cel('button', {type: 'button', key: 'action3', onClick: this.handleDeleteButton}, 
				cel('img', {src: '/app/views/design/icons/delete.png', alt: 'delete icon'}, null)),
		]);
	}
}

class ListOfTasks extends React.Component {
	constructor(props) {
		super(props);
		this.state = {list: []};
	}
	
	componentDidMount() {
		this.setState({list: this.props.list});
	}
	
	componentDidUpdate() {
		if (this.state.list !== this.props.list) {
			this.setState({list: this.props.list});
		}
	}
	
	render() {
		return this.state.list.map((note) => {
			if (note.done == 0) {
				return [cel('div', {key: note.todoId, className: 'checkBoxDiv'}, null), 
						cel('p', {key: note.todoId + 'p'}, note.task)];
			} else {
				return [cel('div', {key: note.todoId, className: 'checkBoxDiv'}, 
						   cel('img', {key: note.todoId + 'done', src: '/app/views/design/icons/check.png', alt: 'check icon'}, null)), 
						cel('p', {key: note.todoId + 'p'}, note.task)];
			}
		});
	}
}

class PersonalNotes extends React.Component {
	constructor(props) {
		super(props);
		this.state = {notes: []};
		this.handleAction = this.handleAction.bind(this);
	}
	
	handleMouseEnter(e) {
		let actions = e.currentTarget.querySelector('div.actions');
		actions.style['display'] = 'inline';
	}
	
	handleMouseLeave(e) {
		let actions = e.currentTarget.querySelector('div.actions');
		actions.style['display'] = '';
	}
	
	handleAction(e, action) {
		this.props.onUserAction(e, action);
	}
	
	componentDidMount() {
		this.setState({notes: this.props.notes});
	}
	
	componentDidUpdate() {
		if (this.state.notes !== this.props.notes) {
			this.setState({notes: this.props.notes});
		}
	}
	
	render() {
		return this.state.notes.map((note) => {
			if (!(Array.isArray(note.content))) {
				return cel('div', {key: note.noteId + '' + note.individ, className: 'card note', 'data-individ': note.individ, onMouseEnter: this.handleMouseEnter, onMouseLeave: this.handleMouseLeave}, [
					cel(NoteAction, {key: 'actions', type: 0, onActionClick: this.handleAction}, null),
					cel('div', {key: note.noteId + 'body' + note.individ, className: 'card-body'}, [
						cel('h5', {key: note.noteId + 'title' + note.individ, className: 'card-title'}, note.title),
						cel('p', {key: note.noteId + 'text' + note.individ, className: 'card-text'}, note.content),
					]),
				]);
			} else {
				return cel('div', {key: note.noteId + '' + note.individ, className: 'card note', 'data-individ': note.individ, onMouseEnter: this.handleMouseEnter, onMouseLeave: this.handleMouseLeave}, [
					cel(NoteAction, {key: 'actions', type: 0, onActionClick: this.handleAction}, null),
					cel('div', {key: note.noteId + 'body' + note.individ, className: 'card-body'}, [
						cel('h5', {key: note.noteId + 'title' + note.individ, className: 'card-title'}, note.title),
						cel(ListOfTasks, {key: 'taskList',list: note.content}, null),
					]),
				]);
			}
		});
	}
}

class GroupNotes extends React.Component {
	constructor(props) {
		super(props);
		this.handleAction = this.handleAction.bind(this);
		this.state = {notes: []};
	}
	
	handleMouseEnter(e) {
		let actions = e.currentTarget.querySelector('div.actions');
		actions.style['display'] = 'inline';
	}
	
	handleMouseLeave(e) {
		let actions = e.currentTarget.querySelector('div.actions');
		actions.style['display'] = '';
	}
	
	handleAction(e, action) {
		this.props.onUserAction(e, action);
	}
	
	componentDidMount() {
		this.setState({notes: this.props.notes});
	}
	
	componentDidUpdate() {
		if (this.state.notes !== this.props.notes) {
			this.setState({notes: this.props.notes});
		}
	}
	
	render() {
		return this.state.notes.map((note) => {
			if (!(Array.isArray(note.content))) {
				return cel('div', {key: note.noteId + '' + note.individ, className: 'card note', 'data-individ': note.individ, onMouseEnter: this.handleMouseEnter, onMouseLeave: this.handleMouseLeave}, [
					cel(NoteAction, {key: 'actions', type: 1, onActionClick: this.handleAction}, null),
					cel('div', {key: note.noteId + 'body' + note.individ, className: 'card-body'}, [
						cel('p', {key: note.noteId + note.groupName, style: {fontSize: '0.8rem', opacity: 0.7}}, note.groupName),
						cel('h5', {key: note.noteId + 'title' + note.individ, className: 'card-title'}, note.title),
						cel('p', {key: note.noteId + 'text' + note.individ, className: 'card-text'}, note.content),
					]),
				]);
			} else {
				return cel('div', {key: note.noteId + '' + note.individ, className: 'card note', 'data-individ': note.individ, onMouseEnter: this.handleMouseEnter, onMouseLeave: this.handleMouseLeave}, [
					cel(NoteAction, {key: 'actions', type: 1, onActionClick: this.handleAction}, null),
					cel('div', {key: note.noteId + 'body' + note.individ, className: 'card-body'}, [
						cel('p', {key: note.noteId + note.groupName, style: {fontSize: '0.8rem', opacity: 0.7}}, note.groupName),
						cel('h5', {key: note.noteId + 'title' + note.individ, className: 'card-title'}, note.title),
						cel(ListOfTasks, {key: 'taskList',list: note.content}, null),
					]),
				]);
			}
		});
	}
}

class ManageGroups extends React.Component {
	constructor(props) {
		super(props);
		this.state = {groups: [], window: {visible: false, data: '', action: ''}};
		this.handleCreateGroupBtn = this.handleCreateGroupBtn.bind(this);
		this.createGroup = this.createGroup.bind(this);
		this.editGroup = this.editGroup.bind(this);
		this.deleteGroup = this.deleteGroup.bind(this);
		this.handleAction = this.handleAction.bind(this);
		this.closeWindow = this.closeWindow.bind(this);
	}
	
	componentDidMount() {
		loadGroups(this, (context, status, groups) => {
			if (status == 200) {
				context.setState({groups: groups});
			}
		});
	}
	
	handleMouseEnter(e) {
		let actions = e.currentTarget.querySelector('div.actions');
		actions.style['display'] = 'inline';
	}
	
	handleMouseLeave(e) {
		let actions = e.currentTarget.querySelector('div.actions');
		actions.style['display'] = '';
	}
	
	handleAction(e, action) {
		let groupID = e.parentElement.parentElement.attributes['data-groupid'].value;
		loadGroup(this, groupID, (context, status, result) => {
			if (status == 200) {
				context.setState({window:{visible: true, data: result, action: action}});
			}
		});
	}
	
	handleCreateGroupBtn() {
		this.setState({window:{visible: true, data: '', action: 'createGroup'}});
	}
	
	createGroup(group) {
		if (group.groupName.length > 0 && group.groupMembers.length > 0) {
			postGroup(this, group, (context, status, result) => {
				if (status == 200) {
					let stateCopy = this.state.groups;
					stateCopy.push(result);
					context.setState({groups: stateCopy});
					context.closeWindow();
				} else {
					alert(result.message + ' ' + result.exception);
				}
			});
		}
	}
	
	editGroup(group) {
		let groups = this.state.groups;
		const toEditIndex = groups.findIndex(oldGroup => (oldGroup.groupID == group.groupID));
		groups[toEditIndex].groupName = group.groupName;
		groups[toEditIndex].members = group.groupMembers;
		if (group.groupName.length > 0 && group.groupMembers.length > 0) {
			putGroup(this, group, (context, status, result) => {
				if (status == 200) {
					context.setState({groups: groups});
					context.closeWindow();
				} else {
					alert(result.message + ' ' + result.exception);
				}
			});
		}
	}
	
	deleteGroup(group) {
		let editgroups = this.state.groups;
		const toDeleteIndex = editgroups.findIndex(grp => (grp.groupID == group.groupID));
		deleteGroup(this, group.groupID, (context, status, result) => {
			if (status == 200) {
				editgroups.splice(toDeleteIndex, 1);
				context.setState({groups: editgroups});
				context.closeWindow();
			} else if (status == 400) {
				alert(result.message + ' ' + result.exception);
			}
		});
		console.log('delete group');
		console.log(group);
	}
	
	closeWindow() {
		this.setState({window: {visible: false, data: '', action: ''}});
	}
	
	render() {
		const window = this.state.window.visible ? cel(Window, {key: 'window', onWindowClose: this.closeWindow, onDeleteGroup: this.deleteGroup, onEditGroup: this.editGroup, onCreateGroup: this.createGroup, data: this.state.window.data, action: this.state.window.action}, null) : null;
		const groups = this.state.groups.map(group => {
			return cel('div', {key: 'group' +  group.groupID, className: 'card note', 'data-groupid': group.groupID, onMouseEnter: this.handleMouseEnter, onMouseLeave: this.handleMouseLeave}, [
				cel(GroupAction, {key: 'actions', onActionClick: this.handleAction}, null),
				cel('div', {key: 'card-body', className: 'card-body'}, [
					cel('div', {key: 'card-title', className: 'card-title'}, group.groupName),
					cel('div', {key: 'card-text', className: 'card-text'}, group.members.length + ' members'),
				])
			])
		});
		const createBtns = [cel('div', {key: 'SideButtonDiv3'}, [
				cel('button', {key: 'sideBar-addGroupBtn', className: 'sidebar-button', onClick: this.handleCreateGroupBtn}, cel('img', {className: 'sidemenu-picture', src: '/app/views/design/icons/create-group.png', alt: 'createGroup'}, null)),
				cel('p', {key: 'sideBar-addGroupBtnT', className: 'sidebar-button-name'}, 'Create group'),
			])];
		return cel('div', {className: 'card-columns'}, [
			window,
			ReactDOM.createPortal(
				createBtns,
				document.getElementById('side-bar')
			),
			groups
		]);
	}
}

class ShowSwitcher extends React.Component {
	constructor(props) {
		super(props);
		this.state = {tab: ''};
		this.handleNotes = this.handleNotes.bind(this);
		this.handleGroups = this.handleGroups.bind(this);
	}
	
	handleNotes() {
		this.setState({tab: 'notes'});
	}
	
	handleGroups() {
		this.setState({tab: 'groups'});
	}
	
	componentDidUpdate() {
		this.props.onPageSwitch(this.state.tab);
	}
	
	render() {
		return cel('div', {key: 'showSwitch', className: 'showSwitchers'}, [
			cel('button', {key: 'showSwitch-btn0', onClick: this.handleNotes}, 'Notes'),
			cel('button', {key: 'showSwitch-btn1', onClick: this.handleGroups}, 'Groups'),
		]);
	}
}

class Notes extends React.Component {
	constructor() {
		super();
		this.state = {notes: [], privateNotes: [], groupNotes: [], window: {visible: false, data: '', action: ''}};
		this.handleUserAction = this.handleUserAction.bind(this);
		this.closeWindow = this.closeWindow.bind(this);
		this.handleAddNote = this.handleAddNote.bind(this);
		this.handleNoteEdit = this.handleNoteEdit.bind(this);
		this.handleNoteShare = this.handleNoteShare.bind(this);
		this.handleNoteDelete = this.handleNoteDelete.bind(this);
		this.openAddNoteWindow = this.openAddNoteWindow.bind(this);
		this.openAddTaskWindow = this.openAddTaskWindow.bind(this);
	}
	
	componentDidMount() {
		loadNotes(this, function(context, data) {
			let pNotes = data.filter(note => (note.type == 0 || note.type == 1) && !('groupId' in note));
			let gNotes = data.filter(note => (note.type == 2 || note.type == 3) || 'groupId' in note);
			context.setState({notes: data, privateNotes: pNotes, groupNotes: gNotes});
		});
	}
	
	openAddNoteWindow() {
		this.setState({window: {visible: true, data: null, action: 'addNote'}});
	}
	
	openAddTaskWindow() {
		this.setState({window: {visible: true, data: null, action: 'addTask'}});
	}
	
	handleUserAction(e, action) {
		let data = {individ: e.parentElement.parentElement.attributes['data-individ'].value};
		data.shared = e.parentElement.parentElement.parentElement.classList[0] == 'groups' ? true : false;
		if (data.shared == false) {
			data.note = this.state.privateNotes.filter(note => note.individ == data.individ)[0];
			if (data.note.type == 0) {
				loadNote(this, data.individ, function(context, data) {
					context.setState({window: {visible: true, data: data, action: action}});
				});
			} else if (data.note.type == 1) {
				loadTask(this, data.individ, function(context, data) {
					context.setState({window: {visible: true, data: data, action: action}});
				});
			}
		} else {
			data.note = this.state.groupNotes.filter(note => note.individ == data.individ)[0];
			if (data.note.type == 2 || data.note.type == 0) {
				loadSharedNote(this, data.individ, data.note.groupId, function(context, data) {
					context.setState({window: {visible: true, data: data, action: action}});
				});
			} else if (data.note.type == 3 || data.note.type == 1) {
				loadSharedTask(this, data.individ, data.note.groupId, function(context, data) {
					context.setState({window: {visible: true, data: data, action: action}});
				});
			}
		}
	}
	
	handleAddNote(note) {
		let newNote = note;
		let type, stateCopy;
		submitNote(this, newNote, 'add', (context, status, result) => {
			if (status == true) {
				if (result.type == 0 || result.type == 1 && !result.hasOwnProperty('groupId')) {
					type = 'privateNotes';
				} else if (result.type == 2 || result.type == 3 || result.hasOwnProperty('groupId')) {
					type = 'groupNotes';
				}
				stateCopy = context.state[type];
				stateCopy.unshift(result);
				context.setState({type: stateCopy});
				context.closeWindow();
			} else {
				alert(result.message + ' ' + result.exception);
			}
		}); 
	}
	
	handleNoteEdit(note) {
		let editedNote = note;
		let type, notes;
		if ((editedNote.type == 0 || editedNote.type == 1) && !editedNote.hasOwnProperty('groupId')) {
			type = 'privateNotes';
		} else if (editedNote.type == 2 || editedNote.type == 3 || editedNote.hasOwnProperty('groupId')) {
			type = 'groupNotes';
		}
		notes = this.state[type];
		const toEditIndex = notes.findIndex(note => (note.noteId == editedNote.noteId));
		notes[toEditIndex] = editedNote;
		submitNote(this, editedNote, 'edit', (context, status) => {
			if (status == true) {
				context.setState({type: notes});
				context.closeWindow();
			} else alert('Failed to save changes.');
		}); 
	}
	
	handleNoteShare(note, gn) {
		shareRecord(this, note.individ, gn, (context, status, result) => {
			if (status == 200) {
				let groupNotes = this.state.groupNotes;
				let shNote = note;
				shNote.groupId = result.groupId;
				shNote.individ = result.individ;
				shNote.groupName = gn;
				groupNotes.push(shNote);
				this.setState({groupNotes: groupNotes});
				context.closeWindow();
			} else {
				alert(result.message + ' ' + result.exception);	
			}
		});
	}
	
	handleNoteDelete(note) {
		const type = note.type == 0 || note.type == 2 ? 'note' : 'task';
		deleteNote(this, note, type, (context, status) => {
			if (status == 200) {
				let stateCopy = !note.hasOwnProperty('groupId') ? this.state.privateNotes : this.state.groupNotes;
				const index = stateCopy.findIndex(toDnote => (toDnote.noteId == note.noteId));
				stateCopy.splice(index, 1);
				if (note.hasOwnProperty('groupId')) this.setState({groupNotes: stateCopy});
				else this.setState({privateNotes: stateCopy});
				context.closeWindow();
			} else alert('Failed to delete note. Make sure that it is not shared.');
		});
	}
	
	closeWindow() {
		this.setState({window: {visible: false, data: '', action: ''}});
	}
	
	render() {
		const window = this.state.window.visible ? cel(Window, {key: 'window', onWindowClose: this.closeWindow, onNoteAdd: this.handleAddNote, onNoteEdit: this.handleNoteEdit, onNoteShare: this.handleNoteShare, onNoteDelete: this.handleNoteDelete, data: this.state.window.data, action: this.state.window.action}, null) : null;
		const createBtns = [cel('div', {key: 'SideButtonDiv0'}, [
				cel('button', {key: 'sideBar-addNoteBtn', className: 'sidebar-button', onClick: this.openAddNoteWindow}, cel('img', {className: 'sidemenu-picture', src: '/app/views/design/icons/add-note.png', alt: 'addNote'}, null)),
				cel('p', {key: 'sideBar-addNoteBtnT', className: 'sidebar-button-name'}, 'Add note'),
				]), cel('div', {key: 'SideButtonDiv1'}, [
				cel('button', {key: 'sideBar-addTaskBtn', className: 'sidebar-button', onClick: this.openAddTaskWindow}, cel('img', {className: 'sidemenu-picture', src: '/app/views/design/icons/add-task.png', alt: 'addTask'}, null)),
				cel('p', {key: 'sideBar-addTaskBtnT', className: 'sidebar-button-name'}, 'Add task'),
			])];
		return [window,
			ReactDOM.createPortal(
				createBtns,
				document.getElementById('side-bar')
			),
			cel('div', {key: 'noteLoadDest', className: 'card-columns'}, 
			cel(PersonalNotes, {notes: this.state.privateNotes, onUserAction: this.handleUserAction}, null)),
			cel('hr', {key: 'hr_divider'}, null),
			cel('div', {key: 'groupNotes', className: 'groups card-columns'},
				cel(GroupNotes, {notes: this.state.groupNotes, onUserAction: this.handleUserAction}, null))];
	}
}

class Content extends React.Component {
	constructor() {
		super();
		this.state = {groups: [], tab: 'notes'};
		this.closeWindow = this.closeWindow.bind(this);
		this.handleSideBar = this.handleSideBar.bind(this);
		this.handleTabSwitch = this.handleTabSwitch.bind(this);
	}
	
	handleSideBar(window) {
		const noteTypes = ['addNote', 'addTask'];
		const groupTypes = ['createGroup'];
		if (noteTypes.indexOf(window.action) != -1)
			this.setState({window: window});
	}
	
	closeWindow() {
		this.setState({window: {visible: false, data: '', action: ''}});
	}
	
	handleTabSwitch(tab) {
		if (this.state.tab !== tab && tab.length > 0) {
			this.setState({tab: tab});
		}
	}
	
	render() {
		let content = [
			cel(ShowSwitcher, {key: 'showSwitch', onPageSwitch: this.handleTabSwitch}, null)];
		content.push(cel(Header, {key: 'header'}, null));
		if (this.state.tab == 'notes') {
		 	content.push(cel(Notes, {key: 'notes'}, null));
		} else if (this.state.tab == 'groups') {
			content.push(cel(ManageGroups, {key: 'manageGroups'}, null));
		}
		return content;
	}
}

ReactDOM.render(
	cel(Content, null, null),
	document.getElementsByClassName('content')[0]
);