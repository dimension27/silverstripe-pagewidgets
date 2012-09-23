<div class="loginWidget gridCell $CSSClasses">
	<div class="bd">
	<% if isLoggedIn %>
		$LoggedInContent
	<% else %>
		<h3>Login</h3>
		<form method="post" action="/Security/LoginForm?BackURL=$BackURL">
			<div>Email: <input type="text" class="email" name="Email" /></div>
			<div>Password: <input type="password" class="password" name="Password" /></div>
			<div class="remember"><input type="checkbox" class="remember-me" name="Remember" /> Remember me?</div>
			<div class="submit"><input type="submit" class="button login" value="&raquo; Login" /></div>
		</form>
		<div class="links">
			<a class="internal white" href="/Security/lostpassword">Lost password</a>
			<% if ShowRegisterLink %><a class="internal white" href="/register">Register</a><% end_if %>
		</div>
	<% end_if %>
	</div>
</div>
