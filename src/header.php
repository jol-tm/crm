<!DOCTYPE html>

<?php

$baseAssetsPath = "/crm/app/assets/";

ini_set("display_errors", 1);
ini_set("session.cookie_lifetime", 3600);
ini_set("session.gc_maxlifetime", 3600);
ini_set("date.timezone", "America/Sao_Paulo");

session_start();

if (!isset($_SESSION["authenticatedUser"]) && ($pageTitle !== "Acesso"))
{
	$_SESSION["notification"] = [
		"message" => "Não autenticado ou sessão expirada!",
		"status" => "failure"
	];
	header("Location: ../acesso/");
	exit();
}

if (isset($_SESSION["authenticatedUser"]) && ($pageTitle === "Acesso"))
{
	header("Location: ../comercial/");
	exit();
}

if (isset($_GET["desconectar"]))
{
	require_once "../../src/User.php";

	$user = new User();

	if ($user->disconnect())
	{
		header("Location: ../acesso/?desconectado");
		exit();
	}
	else
	{
		$_SESSION["notification"] = [
			"message" => "Erro ao desconectar!",
			"status" => "failure"
		];
		header("Location: ./");
	}
}

?>

<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>BMS | <?= $pageTitle; ?></title>
	<link rel="icon" href="<?= $baseAssetsPath . "logo.svg"; ?>" type="image/svg+xml">
	<link rel="stylesheet" href="<?= $baseAssetsPath . "style.css?v4.4"; ?>">
	<script defer src="<?= $baseAssetsPath . "script.js?v3"; ?>"></script>
</head>

<body>

<?php

if (isset($_GET["desconectado"]))
{
	echo "<div class='notification successNotification'>Desconectado com sucesso.</div>";
}

if (isset($_SESSION["notification"]))
{
	echo "<div class='notification {$_SESSION['notification']['status']}" . "Notification" . "'>{$_SESSION['notification']['message']}</div>";
	unset($_SESSION["notification"]);
}

if ($pageTitle !== "Acesso")
{
	$comercialId = $pageTitle === "Comercial" ? "currentPage" : null;
	$financeiroId = $pageTitle === "Financeiro" ? "currentPage" : null;
	$clientesId = $pageTitle === "Clientes" ? "currentPage" : null;

	echo "
	<nav>
		<a id='$clientesId' href='../clientes/'>Clientes</a>
		<a id='$comercialId' href='../comercial/ '>Comercial</a>
		<a id='$financeiroId' href='../financeiro/'>Financeiro</a>
	</nav>
	<header>
		<h5 id='authenticatedUser'>
			{$_SESSION['authenticatedUser']} | <a href='./?desconectar'>Sair</a>
		</h5>
	";
}

?>
