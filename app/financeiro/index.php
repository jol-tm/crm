<?php

$pageTitle = "Financeiro";

require_once "../../src/header.php";
require_once "../../src/Proposta.php";

$proposta = new Proposta();
$propostas = [];
$pesquisa = "";

if (!empty($_GET["q"]))
{
	$propostas = $proposta->pesquisarProposta();
	$pesquisa = $_GET["q"];
}
else
{
	$propostas = $proposta->verPropostasEmFaseFinanceira();
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
			"message" => "Erro na exclusão. Informações inconsistentes!",
			"status" => "failure"	
		];
	}
}

if (isset($_POST["voltarEmAnalise"]))
{
	$proposta->voltarEmAnalise();
}

if (isset($_POST["atualizarStatusProposta"]))
{
	if (filter_var($_POST["id"], FILTER_VALIDATE_INT) && isset($_POST["dataAceiteProposta"]))
	{
		$proposta->atualizarStatusProposta();
	}
	else
	{
		header("Location: ./");
		$_SESSION["notification"] = [
			"message" => "Erro na atualização. Informações inconsistentes!",
			"status" => "failure"	
		];
	}
}

if (isset($_POST["mostrarAtualizarStatus"]) && filter_var($_POST["id"], FILTER_VALIDATE_INT))
{
	$propostaParaAtualizar = $proposta->verProposta($_POST["id"]);

	echo "
	<div class='formWrapper'>
	<form action='' method='post' class='customForm'>
		<input type='hidden' name='id' value='{$propostaParaAtualizar['id']}'>
		<input type='hidden' name='dataAceiteProposta' value='{$_POST['dataAceiteProposta']}'>
		<label for='numeroProposta'>N° da Proposta</label>
		<input type='number' name='numeroProposta' id='numeroProposta' placeholder='Ex: 12325 ou 0 para nulo' max='99999999999' value='{$propostaParaAtualizar['numeroProposta']}'>
		<label for='cliente'>Cliente</label>
		<input type='text' name='cliente' id='cliente' placeholder='Nome do Cliente' maxlength='255' value='{$propostaParaAtualizar['cliente']}'>
		<label for='dataEnvioRelatorio'>Data de Envio do Relatorio</label>
		<input type='date' name='dataEnvioRelatorio' id='dataEnvioRelatorio' value='{$propostaParaAtualizar['dataEnvioRelatorio']}'>
		<label for='valor'>Valor da Proposta</label>
		<input type='number' step='0.01' name='valor' id='valor' placeholder='Ex: 999,99' maxlength='10' value='{$propostaParaAtualizar['valor']}'>
		<label for='numeroNotaFiscal'>NF</label>
		<input type='number' name='numeroNotaFiscal' id='numeroNotaFiscal' placeholder='Ex: 123456789' max='999999999' value='{$propostaParaAtualizar['numeroNotaFiscal']}'>
		<label for='dataPagamento'>Data do Pagamento</label>
		<input type='date' name='dataPagamento' id='dataPagamento' value='{$propostaParaAtualizar['dataPagamento']}'>
		<label for='formaPagamento'>Forma de Pagamento</label>
		<input type='text' name='formaPagamento' id='formaPagamento' placeholder='Ex: Parcelado 2x' maxlength='255' value='{$propostaParaAtualizar['formaPagamento']}'>
		<label for='dataUltimaCobranca'>Data Última Cobrança</label>
		<input type='date' name='dataUltimaCobranca' id='dataUltimaCobranca' value='{$propostaParaAtualizar['dataUltimaCobranca']}'>
		<label for='observacoes'>Observações</label>
		<input type='text' name='observacoes' id='observacoes' placeholder='Ex: Desenvolvimento...' maxlength='255' value='{$propostaParaAtualizar['observacoes']}'>
		<button id='updateStatusBtn' type='submit' name='atualizarStatusProposta'>Atualizar</button>
		<a id='cancelUpdateStatusBtn' href=''>Cancelar</a>
	</form>
	</div>
	";
}

?>

<body>
	
<form id="searchBox" action="" method="get">
	<input type="text" name="q" value="<?= $pesquisa; ?>" placeholder="Ex: Aguardando">
	<button id="searchBtn" type="submit" name="">Pesquisar</button>
</form>
<div class="tableResponsive">
	<table>
		<thead>
			<tr>
				<th>N° proposta</th>
				<th>Cliente</th>
				<th>Valor (R$)</th>
				<th>Data envio proposta</th>
				<th>Data aceite proposta</th>
				<th>Dias em análise</th>
				<th>Status proposta</th>
				<th>N° relatório</th>
				<th>Data envio relatório</th>
				<th>NF</th>
				<th>Data pagamento</th>
				<th>Forma pagamento</th>
				<th>Status pagamento</th>
				<th>Dias aguardando pagamento</th>
				<th>Data última cobrança</th>
				<th>Dias desde última cobrança</th>
				<th>Observações</th>
				<th>Atualizar status</th>
				<th>Voltar para em análise</th>
				<th>Apagar</th>
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
				if (empty($_GET["q"]))
				{
					$dataAceiteProposta = DateTime::createFromFormat("d/m/Y", $proposta["dataAceiteProposta"]);
					$mes = (int)$dataAceiteProposta->format("m");
					$ano = $dataAceiteProposta->format("Y");
				}
				else
				{
					$dataEnvioProposta = DateTime::createFromFormat("d/m/Y", $proposta["dataEnvioProposta"]);
					$mes = (int)$dataEnvioProposta->format("m");
					$ano = $dataEnvioProposta->format("Y");
				}
				
				if ($ultimoMes !== $mes)
				{
					echo "<tr><td colspan='19'><h2>{$meses[$mes]}/$ano</h2></td></tr>";
				}
				
				$ultimoMes = $mes;
				
				if ($proposta["statusPagamento"] === "Aguardando")
				{
					$statusPagamento = "pending";
				}
				elseif ($proposta["statusPagamento"] === "Recebido")
				{
					$statusPagamento = "received";
				}
				elseif ($proposta["statusPagamento"] === "Recusada")
				{
					$statusPagamento = "refused";
				}
				
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

				if ($proposta['statusProposta'] === 'Aceita' && $proposta['statusPagamento'] === 'Aguardando')
				{
					$voltarEmAnalise = "
						<form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<button class='backPendingBtn' type='submit' name='voltarEmAnalise'>←</button>
						</form>";
				}
				else
				{
					$voltarEmAnalise = "N/A";
				}

				echo "
				<tr>
					<td>{$proposta['numeroProposta']}</td>
					<td>" . htmlspecialchars($proposta['cliente']) . "</td>
					<td>{$proposta['valor']}</td>
					<td>{$proposta['dataEnvioProposta']}</td>
					<td>{$proposta['dataAceiteProposta']}</td>
					<td>{$proposta['diasEmAnalise']}</td>
					<td><mark class='$statusProposta'>{$proposta['statusProposta']}</mark></td>
					<td>{$proposta['numeroRelatorio']}</td>
					<td>{$proposta['dataEnvioRelatorio']}</td>
					<td>{$proposta['numeroNotaFiscal']}</td>
					<td>{$proposta['dataPagamento']}</td>
					<td>" . htmlspecialchars($proposta['formaPagamento']) . "</td>
					<td><mark class='$statusPagamento'>{$proposta['statusPagamento']}</mark></td>
					<td>{$proposta['diasAguardandoPagamento']}</td>
					<td>{$proposta['dataUltimaCobranca']}</td>
					<td>{$proposta['diasUltimaCobranca']}</td>
					<td>" . htmlspecialchars($proposta['observacoes']) . "</td>
					<td>
					   <form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<input type='hidden' name='dataAceiteProposta' value='{$proposta['dataAceiteProposta']}'>
							<button class='updateProposalBtn' type='submit' name='mostrarAtualizarStatus'>✎</button>
				       </form>
					</td>
					<td>
						$voltarEmAnalise
					</td>
					<td>
					   <form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<button class='deleteProposalBtn' type='submit' name='excluirProposta' onclick=\"return prompt('ATENÇÃO! Excluir permanentemente proposta N° {$proposta['numeroProposta']} de {$proposta['cliente']}? Caso tenha certeza, digite EXCLUIR abaixo.') === 'EXCLUIR'\">⚠</button>
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
