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

	<button id="showRegisterProposalFormBtn">Cadastrar proposta</button>
</header>

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
					$relatorio = (new Proposta())->gerarRelatorio("$ano-$mes");
					echo "<tr><td colspan='10'><h2>{$meses[$mes]}/$ano</h2></td></tr>";
					echo "<tr><td colspan='10'><h4>{$relatorio['propostasEnviadas']} propostas enviadas, {$relatorio['propostasAceitas']} aceitas</h4></td></tr>";
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
							<button class='aproveProposalBtn' type='submit' name='aceitarProposta'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'><polyline points='20 6 9 17 4 12'></polyline></svg>  
							</button>
						</form>
					</td>
					<td>
						<form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<input type='hidden' name='dataEnvioProposta' value='{$proposta['dataEnvioProposta']}'>
							<button  class='denyProposalBtn' type='submit' name='recusarProposta'>
    							<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-x'><line x1='18' y1='6' x2='6' y2='18'></line><line x1='6' y1='6' x2='18' y2='18'></line></svg>
							</button>
						</form>
					</td>
					<td>
					   <form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<button class='deleteProposalBtn type='submit' name='excluirProposta' onclick=\"return prompt('ATENÇÃO! EXCLUSÃO É PERMANENTE! Digite EXCLUIR para confirmar.') === 'EXCLUIR'\">
							    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-trash-2'><polyline points='3 6 5 6 21 6'></polyline><path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'></path><line x1='10' y1='11' x2='10' y2='17'></line><line x1='14' y1='11' x2='14' y2='17'></line></svg>
							</button>
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
