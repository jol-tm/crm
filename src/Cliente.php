<?php

require_once "DatabaseConnection.php";
require_once "DataRepository.php";

class Cliente
{
	private ?object $connection = null;
	private ?object $data = null;
	
	public function __construct()
	{
		$this->connection = new DatabaseConnection();
		$this->data = new DataRepository($this->connection->start());
	}
	
	public function verCliente(int $id): array|false
	{
		return $this->data->read("clientes", "WHERE id = $id")[0];
	}
	
	public function verClientes(): array|false
	{
		$clientes = $this->data->read("clientes", "ORDER BY nome ASC");
		
		foreach ($clientes as &$cliente)
		{
			$cliente['cpf_cnpj'] === null ? $cliente['cpf_cnpj'] = '-' : null;
			$cliente['razaoSocial'] === null ? $cliente['razaoSocial'] = '-' : null;
			$cliente['emailContato'] === null ? $cliente['emailContato'] = '-' : null;
			$cliente['emailNF'] === null ? $cliente['emailNF'] = '-' : null;
			$cliente['telefone'] === null ? $cliente['telefone'] = '-' : null;
			$cliente['endereco'] === null ? $cliente['endereco'] = '-' : null;
		}
		
		return $clientes;
	}

	public function cadastrarCliente(): bool
	{
		$create = $this->data->create("clientes", [
			"nome" => $_POST["nome"],
			"cpf_cnpj" => empty($_POST["cpf_cnpj"]) ? null : $_POST["cpf_cnpj"],
			"razaoSocial" => empty($_POST["razaoSocial"]) ? null : $_POST["razaoSocial"],
			"emailContato" => empty($_POST["emailContato"]) ? null : $_POST["emailContato"],
			"emailNF" => empty($_POST["emailNF"]) ? null : $_POST["emailNF"],
			"telefone" => empty($_POST["telefone"]) ? null : $_POST["telefone"],
			"endereco" => empty($_POST["endereco"]) ? null : $_POST["endereco"],
		]);

		if ($create["success"] === true)
		{
			$_SESSION["notification"] = [
				"message" => "Cliente cadastrado com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}

		$_SESSION["notification"] = [
			"message" => "Erro ao cadastrar cliente. Cód: {$create['errorCode']}",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}

	public function atualizarCliente(): bool
	{  	
		print_r($_POST);
		$update = $this->data->update("clientes", [
				"nome" => empty($_POST["nome"]) ? null : $_POST["nome"],
				"cpf_cnpj" => empty($_POST["cpf_cnpj"]) ? null : $_POST["cpf_cnpj"],
				"razaoSocial" => empty($_POST["razaoSocial"]) ? null : $_POST["razaoSocial"],
				"emailContato" => empty($_POST["emailContato"]) ? null : $_POST["emailContato"],
				"emailNF" => empty($_POST["emailNF"]) ? null : $_POST["emailNF"],
				"telefone" => empty($_POST["telefone"]) ? null : $_POST["telefone"],
				"endereco" => empty($_POST["endereco"]) ? null : $_POST["endereco"],
			],
			[
				"id" => $_POST["id"]
			]
		);

		if ($update["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Cliente atualizado com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}
		
		$_SESSION["notification"] = [
			"message" => "Erro ao atualizar cliente. Nada modificado. Cód: {$update['errorCode']}",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}
	
	public function excluirCliente(): bool
	{
		$delete = $this->data->delete("clientes", ["id" => $_POST["id"]]);

		if ($delete["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Cliente excluído com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}
		
		$_SESSION["notification"] = [
			"message" => "Erro ao excluir cliente. Nada modificado.",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}
}
