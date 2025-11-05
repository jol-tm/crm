<?php

$pageTitle = "Comercial";

require_once "../../src/header.php";
require_once "../../src/Proposta.php";

$proposta = new Proposta();
$propostas = $proposta->verPropostasEmFaseComercial();

if (isset($_POST["cadastrarProposta"]))
{
	if (!empty($_POST["dataEnvioProposta"]) && !empty($_POST["valor"]) && !empty($_POST["cliente"]))
	{
		$proposta->cadastrarProposta();
	}
	else
	{
		header("Location: ./");
		$_SESSION["notification"] = [
			"message" => "Erro no cadastro! Informações incompletas!",
			"status" => "failure"	
		];
	}
}

if (isset($_POST["aceitarProposta"]))
{
	if (filter_var($_POST["id"], FILTER_VALIDATE_INT) && isset($_POST["dataEnvioProposta"]))
	{
		$proposta->aceitarProposta();
	}
	else
	{
		header("Location: ./");
		$_SESSION["notification"] = [
			"message" => "Erro no aceite! Informações inconsistentes!",
			"status" => "failure"	
		];
	}
}


if (isset($_POST["recusarProposta"]))
{
	if (filter_var($_POST["id"], FILTER_VALIDATE_INT) && isset($_POST["dataEnvioProposta"]))
	{
		$proposta->recusarProposta();
	}
	else
	{
		header("Location: ./");
		$_SESSION["notification"] = [
			"message" => "Erro na recusa! Informações inconsistentes!",
			"status" => "failure"	
		];
	}
}

if (isset($_POST["excluirProposta"]))
{
	if (filter_var($_POST["id"], FILTER_VALIDATE_INT))
	{
		$proposta->excluirProposta();
	}
	else
	{
		header("Location: ./");
		$_SESSION["notification"] = [
			"message" => "Erro na exclusão! Informações inconsistentes!",
			"status" => "failure"	
		];
	}
}

?>

<body>

<button id="showRegisterProposalFormBtn">Cadastrar Proposta</button>
<div id="registerProposalForm" class="formWrapper">
	<form action="" method="post" class="customForm">
		<h2>Cadastrar Proposta</h2>
		<label for="numeroProposta">N° da Proposta</label>
		<input type="number" name="numeroProposta" id="numeroProposta" placeholder="Ex: 12325 ou 0 para nulo" max="99999999999" required>
		<label for="dataEnvioProposta">Data de Envio da Proposta</label>
		<input type="date" name="dataEnvioProposta" id="dataEnvioProposta" value="<?= (new DateTime())->format("Y-m-d"); ?>" required>
		<label for="valor">Valor da Proposta</label>
		<input type="number" step="0.01" name="valor" id="valor" placeholder="Ex: 999,99" maxlength="10" required>
		<label for="cliente">Cliente</label>
		<input type="text" name="cliente" id="cliente" placeholder="Nome do Cliente" maxlength="255" required>
		<label for="observacoes">Observações</label>
		<input type="text" name="observacoes" id="observacoes" placeholder="Ex: Desenvolvimento..." maxlength="255">
		<button id="registerProposalBtn" type="submit" name="cadastrarProposta">Cadastrar</button>
		<button id="cancelRegisterProposalBtn" type="button">Cancelar</button>
	</form>
</div>
<div class="tableResponsive">
	<table>
		<thead>
			<tr>
				<th>N° proposta</th>
				<th>Cliente</th>
				<th>Valor (R$)</th>
				<th>Data envio proposta</th>
				<th>Dias em análise</th>
				<th>Status</th>
				<th>Observações</th>
				<th>Aceitar</th>
				<th>Recusar</th>
				<th>Excluir</th>
			</tr>
		</thead>
		<tbody>

			<?php
			
			$meses = [
				1 => "Janeiro",
				2 => "Fevereiro",
				3 => "Março",
				4 => "Abril",
				5 => "Maio",
				6 => "Junho",
				7 => "Julho",
				8 => "Agosto",
				9 => "Setembro",
				10 => "Outubro",
				11 => "Novembro",
				12 => "Dezembro"
			];
			$ultimoMes = 0;
			
			foreach ($propostas as $proposta)
			{
				$dataEnvioProposta = DateTime::createFromFormat("d/m/Y", $proposta["dataEnvioProposta"]);
				$mes = (int)$dataEnvioProposta->format("m");
				$ano = $dataEnvioProposta->format("Y");
				
				if ($ultimoMes !== $mes)
				{
					echo "<tr><td colspan='19'><h2>{$meses[$mes]}/$ano</h2></td></tr>";
				}
				
				$ultimoMes = $mes;
				
				if ($proposta["statusProposta"] === "Recusada")
				{
					$statusProposta = "refused";
				}
				elseif ($proposta["statusProposta"] === "Aceita")
				{
					$statusProposta = "accepted";
				}
				else
				{
					$statusProposta = "pending";
				}
				
				echo "
				<tr>
					<td>{$proposta['numeroProposta']}</td>
					<td>" . htmlspecialchars($proposta['cliente']) . "</td>
					<td>{$proposta['valor']}</td>
					<td>{$proposta['dataEnvioProposta']}</td>
					<td>{$proposta['diasEmAnalise']}</td>
					<td><mark class='{$statusProposta}'>{$proposta['statusProposta']}</mark></td>
					<td>" . htmlspecialchars($proposta['observacoes']) . "</td>
					<td>
						<form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<input type='hidden' name='dataEnvioProposta' value='{$proposta['dataEnvioProposta']}'>
							<button class='aproveProposalBtn' type='submit' name='aceitarProposta'>✓</button>
						</form>
					</td>
					<td>
						<form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<input type='hidden' name='dataEnvioProposta' value='{$proposta['dataEnvioProposta']}'>
							<button  class='denyProposalBtn' type='submit' name='recusarProposta'>✕</button>
						</form>
					</td>
					<td>
					   <form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<button class='deleteProposalBtn type='submit' name='excluirProposta' onclick=\"return prompt('ATENÇÃO! Excluir permanentemente proposta N° {$proposta['numeroProposta']} de {$proposta['cliente']}? Caso tenha certeza, digite EXCLUIR abaixo.') === 'EXCLUIR'\">⚠</button>
					   </form>
					</td>
				</tr>
				";
			}

			?>

		</tbody>
	</table>
</div>

</body>

</html>
