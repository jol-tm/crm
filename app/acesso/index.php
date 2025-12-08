<?php

$pageTitle = 'Acesso';

require_once '../../src/header.php';

if (isset($_POST['acessar']) && !empty($_POST['email']) && !empty($_POST['senha']))
{
	require_once '../../src/User.php';
	$user = new User();

	if ($user->authenticate('usuarios', ['email' => $_POST['email']], ['senha' => $_POST['senha']]))
	{
		header('Location: ../../app/comercial/');
	}
	else
	{
		$_SESSION['notification'] = [
			'message' => 'Erro na autenticação. Verifique as credenciais.',
			'status' => 'failure'			
		];
		header('Location: ../../app/acesso/');
	}
}

?>

<body id='bodyLogin'>
	<form id='formLogin' action='' method='post'>
		<h1>Acesso</h1>
		<input type='email' name='email' placeholder='Email' required>
		<input type='password' name='senha' placeholder='Senha' required>
		<button id='loginBtn' type='submit' name='acessar'>Acessar</button>
	</form>
</body>

</html>
