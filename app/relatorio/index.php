<?php

$pageTitle = 'Relatório';

require_once '../../src/header.php';
require_once '../../src/Proposta.php';

?>

<body>
	<form action='' method='get'>
		<label for='data'>Mês e Ano (Qualquer dia)</label>
		<input id='data' name='data' type='date' value="<?= (new DateTime())->format("Y-m-d"); ?>" required>
		<button type='submit'>Gerar relatório</button>
	</form>
	
	<?php
		if (isset($_GET['data']))
		{
			$proposta = new Proposta();
			$relatorio = $proposta->gerarRelatorio($_GET['data']);
			echo "
				<h2>Relatório do mês {$relatorio['data']}</h2>
				<div class='tableResponsive'>
					<table>
						<thead>
							<tr>
								<th>Propostas enviadas</th>
								<th>Propostas aceitas *</th>
								<th>Valor recebido (R$) **</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{$relatorio['propostasEnviadas']}</td>
								<td>{$relatorio['propostasAceitas']}</td>
								<td>{$relatorio['valorRecebido']}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<h6>* Propostas aceitas nesse mês podem ser referentes a meses anteriores.</h6>
				<h6>** O valor recebido apenas considera pagamentos finalizados completamente, parcelas não estão incluídas. Valores recebidos nesse mês podem ser referentes a propostas de meses anteriores</h6>
			";
		}	
	?>
</body>

</html>
