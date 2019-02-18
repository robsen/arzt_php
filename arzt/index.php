<?php
	if (isset($_POST['login'])
		&& isset($_POST['username'])
		&& isset($_POST['password']))
	{
		require_once 'includes/db.inc.php';
		$user = DB::Call('get_login_credentials', [$_POST['username']], false);
		
		if (isset($user['password'])
			&& $user['password'] === $_POST['password'])
		{
			session_start();
			$_SESSION['userId'] = $user['id'];
			header('location:dashboard.php');
			exit(0);
		}
		else
		{
			$_GET['message'] = 'Benutzername oder Passwort falsch!';
		}
	}
?>
<!DOCTYPE html>
<html lang=de>
	<head>
		<meta charset=utf-8>
		<title>Arzt - Login</title>
		
		<link rel=stylesheet href=/css/main.css>
		<link rel=stylesheet href=/css/index.css>
	</head>
	<body>
		<form action=/index.php method=post>
			<label for=username>Benutzername</label>
			<input type=text id=username name=username required autofocus value="<?= Username() ?>">
			
			<label for=password>Passwort</label>
			<input type=password id=password name=password required>
			
			<input type=submit name=login value=Login>
		</form>
		
		<?php if (isset($_GET['message'])): ?>
		<p id=message><?= htmlspecialchars($_GET['message']) ?></p>
		<?php endif ?>
	</body>
</html>
<?php
	
	function Username()
	{
		if (isset($_POST['username'])
			&& !empty($_POST['username']))
		{
			return $_POST['username'];
		}
		return '';
	}