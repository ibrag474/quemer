<div class="main-card">
	<div class="form-div">
		<h3>Reset Password</h3>
		<p id="res"></p>
		<p id="errmsg" style="color:red"></p>
		<form action="/" name="pswdResetForm" onsubmit="submitPswdReset(); return false;">
			<input name="code" type="text" placeholder="Code" required>
			<input id="pswd" name="password" type="password" placeholder="Password" required>
			<input id="repswd" name="re-password" type="password" placeholder="Re-type your password" required>
			<input id="submit" type="submit" value="Reset">
		</form>
	</div>
</div>