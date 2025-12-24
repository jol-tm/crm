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
	$proposta->excluirProposta();
}

if (isset($_POST["voltarEmAnalise"]))
{
	$proposta->voltarEmAnalise();
}

if (isset($_POST["atualizarStatusProposta"]))
{
	$proposta->atualizarStatusProposta();
}

if (isset($_POST["mostrarAtualizarStatus"]))
{
	require_once "../../src/Cliente.php";
	
	$propostaParaAtualizar = $proposta->verProposta($_POST["id"]);
	$clientes = (new Cliente())->verClientes();
	$selectClientes = "";
	
	foreach ($clientes as $cliente)
	{
		$selected = $propostaParaAtualizar['idCliente'] === $cliente['id'] ? "selected" : null; 
		$selectClientes .= "<option $selected value={$cliente['id']}>{$cliente['nome']}</option>";
	}

	echo "
	<div class='containerForm'>
	<form action='' method='post' class='customForm'>
		<input type='hidden' name='id' value='{$propostaParaAtualizar['id']}'>
		<input type='hidden' name='dataAceiteProposta' value='{$_POST['dataAceiteProposta']}'>
		<label for='numeroProposta'>N° da Proposta</label>
		<input type='number' name='numeroProposta' id='numeroProposta' placeholder='Ex: 12325 ou 0 para nulo' min='0' value='{$propostaParaAtualizar['numeroProposta']}'>
		<label for='cliente'>Cliente</label>
		<select name='cliente' id='cliente'>
			$selectClientes
		</select>
		<label for='valor'>Valor da Proposta</label>
		<input type='number' step='0.01' name='valor' id='valor' placeholder='Ex: 999,99' min='0' value='{$propostaParaAtualizar['valor']}'>
		<label for='dataPagamento'>Data do Pagamento</label>
		<input type='date' name='dataPagamento' id='dataPagamento' value='{$propostaParaAtualizar['dataPagamento']}'>
		<label for='formaPagamento'>Forma de Pagamento</label>
		<input type='text' name='formaPagamento' id='formaPagamento' placeholder='Ex: Parcelado 2x' maxlength='255' value='{$propostaParaAtualizar['formaPagamento']}'>
		<label for='observacoes'>Observações</label>
		<input type='text' name='observacoes' id='observacoes' placeholder='Ex: Desenvolvimento...' maxlength='255' value='{$propostaParaAtualizar['observacoes']}'>
		<button id='botaoAtualizarStatusProposta' type='submit' name='atualizarStatusProposta'>Atualizar</button>
		<a id='botaoCancelarAtualizarStatusProposta' href=''>Cancelar</a>
	</form>
	</div>
	";
}

?>
	
	<form id="caixaPesquisa" action="" method="get">
		<input type="text" name="q" value="<?= $pesquisa; ?>" placeholder="Ex: Aguardando">
		<button id="botaoPesquisa" type="submit" name="">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
		</button>
	</form>
</header>

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
				<th>Data pagamento</th>
				<th>Forma pagamento</th>
				<th>Status pagamento</th>
				<th>Dias aguardando pagamento</th>
				<th>Observações</th>
				<th>Atualizar status</th>
				<th>Voltar para em análise</th>
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
					$relatorio = (new Proposta())->gerarRelatorio("$ano-$mes");
					echo "<tr><td colspan='20'><h2>{$meses[$mes]}/$ano</h2></td></tr>";
					echo "<tr><td colspan='20'><h4>{$relatorio['propostasEnviadas']} propostas enviadas, {$relatorio['propostasAceitas']} aceitas, R$ {$relatorio['valorRecebido']} recebido de R$ {$relatorio['valorTotal']}</h4></td></tr>";
				}
				
				$ultimoMes = $mes;
				
				if ($proposta["statusPagamento"] === "Aguardando")
				{
					$statusPagamento = "aguardando";
				}
				elseif ($proposta["statusPagamento"] === "Recebido")
				{
					$statusPagamento = "recebido";
				}
				elseif ($proposta["statusPagamento"] === "Recusada")
				{
					$statusPagamento = "recusada";
				}
				
				if ($proposta["statusProposta"] === "Recusada")
				{
					$statusProposta = "recusada";
				}
				elseif ($proposta["statusProposta"] === "Aceita")
				{
					$statusProposta = "aceita";
				}
				else
				{
					$statusProposta = "emAnalise";
				}

				if ($proposta['statusProposta'] === 'Aceita' && $proposta['statusPagamento'] === 'Aguardando')
				{
					$voltarEmAnalise = "
						<form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<button class='botaoVoltarEmAnalise' type='submit' name='voltarEmAnalise' onclick=\"return confirm('Voltar para em análise fará com que as contagens de dias sejam redefinidas. Prosseguir?')\">
							    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-corner-up-left'><polyline points='9 14 4 9 9 4'></polyline><path d='M20 20v-7a4 4 0 0 0-4-4H4'></path></svg>
							</button>
						</form>";
				}
				else
				{
					$voltarEmAnalise = "N/A";
				}

				echo "
				<tr>
					<td>{$proposta['numeroProposta']}</td>
					<td>" . htmlspecialchars($proposta['nomeCliente']) . "</td>
					<td>{$proposta['valor']}</td>
					<td>{$proposta['dataEnvioProposta']}</td>
					<td>{$proposta['dataAceiteProposta']}</td>
					<td>{$proposta['diasEmAnalise']}</td>
					<td><mark class='$statusProposta'>{$proposta['statusProposta']}</mark></td>
					<td>{$proposta['dataPagamento']}</td>
					<td>" . htmlspecialchars($proposta['formaPagamento']) . "</td>
					<td><mark class='$statusPagamento'>{$proposta['statusPagamento']}</mark></td>
					<td>{$proposta['diasAguardandoPagamento']}</td>
					<td>" . htmlspecialchars($proposta['observacoes']) . "</td>
					<td>
					   <form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<input type='hidden' name='dataAceiteProposta' value='{$proposta['dataAceiteProposta']}'>
							<button class='botaoMostrarAtualizarStatus' type='submit' name='mostrarAtualizarStatus'>
							    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-edit-3'><path d='M12 20h9'></path><path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z'></path></svg>
							</button>
				       </form>
					</td>
					<td>
						$voltarEmAnalise
					</td>
					<td>
					   <form action='' method='post'>
							<input type='hidden' name='id' value='{$proposta['id']}'>
							<button class='botaoExcluirProposta' type='submit' name='excluirProposta' onclick=\"return prompt('ATENÇÃO! EXCLUSÃO É PERMANENTE! Digite EXCLUIR para confirmar.') === 'EXCLUIR'\">
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
