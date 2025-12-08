<?php

$pageTitle = "Clientes";

require_once "../../src/header.php";
require_once "../../src/Cliente.php";

$cliente = new Cliente();

if (isset($_POST["registerClientBtn"]))
{
	if (!empty($_POST["nome"]))
	{
		$cliente->cadastrarCliente();
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

if (isset($_POST["atualizarCliente"]))
{
	if (filter_var($_POST["id"], FILTER_VALIDATE_INT))
	{
		$cliente->atualizarCliente();
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

if (isset($_POST["excluirCliente"]))
{
	$cliente->excluirCliente();
}

if (isset($_POST["mostrarAtualizarCliente"]) && filter_var($_POST["id"], FILTER_VALIDATE_INT))
{
	$clienteParaAtualizar = $cliente->verCliente($_POST["id"]);

	echo "
	<div class='formWrapper'>
	<form action='' method='post' class='customForm'>
		<input type='hidden' name ='id' value='{$clienteParaAtualizar['id']}'>
		<label for='nome'>Nome</label>
		<input type='text' name='nome' id='nome' placeholder='' maxlength='255' value='{$clienteParaAtualizar['nome']}'>
		<label for='cpf_cnpj'>CPF / CNPJ</label>
		<input type='text' name='cpf_cnpj' id='cpf_cnpj' placeholder='Número sem pontuações' maxlength='14' value='{$clienteParaAtualizar['cpf_cnpj']}'>
		<label for='razaoSocial'>Razão social</label>
		<input type='text' name='razaoSocial' id='razaoSocial' placeholder='' maxlength='255' value='{$clienteParaAtualizar['razaoSocial']}'>
		<label for='emailContato'>Email contato</label>
		<input type='email' name='emailContato' id='emailContato' placeholder='' maxlength='255' value='{$clienteParaAtualizar['emailContato']}'>
		<label for='emailNF'>Email NF</label>
		<input type='email' name='emailNF' id='emailNF' placeholder='' maxlength='255' value='{$clienteParaAtualizar['emailNF']}'>
		<label for='telefone'>Telefone</label>
		<input type='text' name='telefone' id='telefone' placeholder='' maxlength='15' value='{$clienteParaAtualizar['telefone']}'>
		<label for='endereco'>Endereço</label>
		<input type='text' name='endereco' id='endereco' placeholder='' maxlength='255' value='{$clienteParaAtualizar['endereco']}'>
		<button id='updateClientBtn' type='submit' name='atualizarCliente'>Atualizar</button>
		<a id='cancelUpdateClientBtn' href=''>Cancelar</a>
	</form>
	</div>
	";
}

?>

	<button id="showRegisterClientFormBtn">Cadastrar cliente</button>
</header>

<div id="registerClientForm" class="formWrapper">
	<form action="" method="post" class="customForm">
		<h2>Cadastrar Cliente</h2>
		<label for="nome">Nome</label>
		<input type="text" name="nome" id="nome" placeholder="" maxlength="255" required>
		<label for="cpf_cnpj">CPF / CNPJ</label>
		<input type="text" name="cpf_cnpj" id="cpf_cnpj" placeholder="Número sem pontuações" maxlength="14">
		<label for="razaoSocial">Razão social</label>
		<input type="text" name="razaoSocial" id="razaoSocial" placeholder="" maxlength="255">
		<label for="emailContato">Email contato</label>
		<input type="email" name="emailContato" id="emailContato" placeholder="" maxlength="255">
		<label for="emailNF">Email NF</label>
		<input type="email" name="emailNF" id="emailNF" placeholder="" maxlength="255">
		<label for="telefone">Telefone</label>
		<input type="text" name="telefone" id="telefone" placeholder="" maxlength="15">
		<label for="endereco">Endereço</label>
		<input type="text" name="endereco" id="endereco" placeholder="" maxlength="255">
		<button id="registerClientBtn" type="submit" name="registerClientBtn">Cadastrar</button>
		<button id="cancelRegisterClientBtn" type="button">Cancelar</button>
	</form>
</div>

<div class="tableResponsive">
	<table>
		<thead>
			<tr>
				<th>Cliente</th>
				<th>CPF/CNPJ</th>
				<th>Razão social</th>
				<th>Email contato</th>
				<th>Email NF</th>
				<th>Telefone</th>
				<th>Endereço</th>
				<th>Atualizar</th>
				<th>Excluir</th>
			</tr>
		</thead>
		<tbody>
	<?php

		$clientes = $cliente->verClientes();
		
		foreach ($clientes as $cliente)
		{
			echo "
			<tr>
				<td>" . htmlspecialchars($cliente["nome"]) . "</td>
				<td>" .  htmlspecialchars($cliente["cpf_cnpj"]) . "</td>
				<td>" .  htmlspecialchars($cliente["razaoSocial"]) . "</td>
				<td>" .  htmlspecialchars($cliente["emailContato"]) . "</td>
				<td>" .  htmlspecialchars($cliente["emailNF"]) . "</td>
				<td>" .  htmlspecialchars($cliente["telefone"]) . "</td>
				<td>" .  ($cliente["endereco"]) . "</td>
				<td>
					<form action='' method='post'>
						<input type='hidden' name='id' value='{$cliente['id']}'>
						<button class='updateClientBtn' type='submit' name='mostrarAtualizarCliente'>
							<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-edit-3'><path d='M12 20h9'></path><path d='M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z'></path></svg>
						</button>
					</form>
				</td>
				<td>
					<form action='' method='post'>
						<input type='hidden' name='id' value='{$cliente['id']}'>
						<button class='deleteClientBtn' type='submit' name='excluirCliente' onclick=\"return prompt('ATENÇÃO! EXCLUSÃO É PERMANENTE! Digite EXCLUIR para confirmar.') === 'EXCLUIR'\">
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
