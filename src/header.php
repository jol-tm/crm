<!DOCTYPE html>

<?php

$baseAssetsPath = '/crm/app/assets/';
$accessAllowed = 1;

if (!$accessAllowed)
{
    $sessionPath = ini_get('session.save_path') . '/sess_*';
    $files = glob($sessionPath);

    foreach ($files as $file) {
        unlink($file);
    }
    
   	echo 'O acesso ao sistema foi bloqueado temporariamente. Tente novamente mais tarde ou contate o administrador.';
    exit();   
}

ini_set('display_errors', 1);
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
ini_set('date.timezone', 'America/Sao_Paulo');

session_start();

if (!isset($_SESSION['authenticatedUser']) && ($pageTitle !== 'Acesso'))
{
	$_SESSION['notification'] = [
		'message' => 'Não autenticado ou sessão expirada!',
		'status' => 'failure'
	];
	header('Location: ../acesso/');
	exit();
}

if (isset($_SESSION['authenticatedUser']) && ($pageTitle === 'Acesso'))
{
	header('Location: ../comercial/');
	exit();
}

if (isset($_POST['sair']))
{
	require_once '../../src/User.php';
	$user = new User();

	if ($user->disconnect())
	{
		header('Location: ../acesso/?desconectado');
		exit();
	}
	else
	{
		$_SESSION['notification'] = [
			'message' => 'Erro ao desconectar!',
			'status' => 'failure'
		];
		header('Location: ./');
	}
}

?>

<html lang='pt-br'>

<head>
	<meta charset='UTF-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<title>BMS | <?= $pageTitle; ?></title>
	<link rel='icon' href='<?= $baseAssetsPath . 'logo.svg'; ?>' type='image/svg+xml'>
	<link rel='stylesheet' href='<?= $baseAssetsPath . 'style.css?v5'; ?>'>
	<script defer src='<?= $baseAssetsPath . 'script.js?v5'; ?>'></script>
</head>

<body>

<?php

if (isset($_GET['desconectado']))
{
	echo "<div class='notification successNotification'>Desconectado com sucesso.</div>";
}

if (isset($_SESSION['notification']))
{
	echo "<div class='notification {$_SESSION['notification']['status']}" . 'Notification' . "'>{$_SESSION['notification']['message']}</div>";
	unset($_SESSION['notification']);
}

if ($pageTitle !== 'Acesso')
{
	$comercialId = $pageTitle === 'Comercial' ? 'currentPage' : null;
	$financeiroId = $pageTitle === 'Financeiro' ? 'currentPage' : null;
	$clientesId = $pageTitle === 'Clientes' ? 'currentPage' : null;

	echo "
	<nav>
		<a id='$clientesId' href='../clientes/'>Clientes</a>
		<a id='$comercialId' href='../comercial/ '>Comercial</a>
		<a id='$financeiroId' href='../financeiro/'>Financeiro</a>
	</nav>
	<header>
		<form id='formSair' action='' method='post'> 
			<button id='botaoSair' name='sair' type='submit'>
				<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-log-out'><path d='M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4'></path><polyline points='16 17 21 12 16 7'></polyline><line x1='21' y1='12' x2='9' y2='12'></line></svg>
			</button>
		</form>
	";
}

?>
