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
		return $this->data->read("clientes");
	}
	
	//~ public function pesquisarProposta(): array|false
	//~ {
		//~ $propostas = $this->data->search("propostas", [
			//~ "numeroProposta",
			//~ "numeroNotaFiscal",
			//~ "statusPagamento",
			//~ "valor",
			//~ "cliente",
			//~ "observacoes",
		//~ ], $_GET["q"], "ORDER BY dataEnvioProposta DESC;");
		
		//~ $hoje = (new DateTime())->setTime(0, 0, 0);
		
		//~ foreach ($propostas as &$proposta)
		//~ {
			//~ if ($proposta["dataAceiteProposta"] !== null)
			//~ {
				//~ $proposta["dataAceiteProposta"] = new DateTime($proposta["dataAceiteProposta"]);
				//~ // Se statusPagamento é Aguardando calcula dias com base em hoje, senão pega o dado que foi salvo no banco quando recebeu o pagamento
				//~ $proposta["diasAguardandoPagamento"] = $proposta["statusPagamento"] === "Aguardando" ? $hoje->diff($proposta["dataAceiteProposta"])->days : $proposta["diasAguardandoPagamento"];
				//~ $proposta["dataAceiteProposta"] = $proposta["dataAceiteProposta"]->format("d/m/Y");
			//~ }
			
			//~ if ($proposta["dataUltimaCobranca"] !== null)
			//~ {
				//~ $proposta["dataUltimaCobranca"] = new DateTime($proposta["dataUltimaCobranca"]);
				
				//~ if ($proposta["statusPagamento"] === "Aguardando")
				//~ {
					//~ // Só calcula e consequentente só mostra diasUltimaCobranca enquanto status é Aguardando
					//~ $proposta["diasUltimaCobranca"] = $hoje->diff($proposta["dataUltimaCobranca"])->days;
				//~ }
				
				//~ $proposta["dataUltimaCobranca"] = $proposta["dataUltimaCobranca"]->format("d/m/Y");
			//~ }
			
			//~ if ($proposta["dataEnvioRelatorio"] !== null)
			//~ {
				//~ $proposta["dataEnvioRelatorio"] = (new DateTime($proposta["dataEnvioRelatorio"]))->format("d/m/Y");
			//~ }
			
			//~ if ($proposta["dataPagamento"] !== null)
			//~ {
				//~ $proposta["dataPagamento"] = (new DateTime($proposta["dataPagamento"]))->format("d/m/Y");
			//~ }
			
			//~ // Não há um "if not null" aqui como nas acima porque dataEnvioProposta nunca será nulo
			//~ $proposta["dataEnvioProposta"] = new DateTime($proposta["dataEnvioProposta"]);
			
			//~ empty($proposta["numeroProposta"]) ? $proposta["numeroProposta"] = "-" : null;
			//~ empty($proposta["dataAceiteProposta"]) ? $proposta["dataAceiteProposta"] = "-" : null;
			//~ empty($proposta["dataUltimaCobranca"]) ? $proposta["dataUltimaCobranca"] = "-" : null;
			//~ empty($proposta["dataEnvioRelatorio"]) ? $proposta["dataEnvioRelatorio"] = "-" : null;
			//~ empty($proposta["dataPagamento"]) ? $proposta["dataPagamento"] = "-" : null;
			//~ empty($proposta["numeroNotaFiscal"]) ? $proposta["numeroNotaFiscal"] = "-" : null;
			//~ empty($proposta["formaPagamento"]) ? $proposta["formaPagamento"] = "-" : null;
			//~ empty($proposta["numeroRelatorio"]) ? $proposta["numeroRelatorio"] = "-" : null;
			//~ empty($proposta["observacoes"]) ? $proposta["observacoes"] = "-" : null;
			//~ // isset() em vez de empty() para considerar 0. Lembrando que empty(0) retorna true
			//~ isset($proposta["diasAguardandoPagamento"]) ? null : $proposta["diasAguardandoPagamento"] = "-";
			//~ isset($proposta["diasUltimaCobranca"]) ? null : $proposta["diasUltimaCobranca"] = "-";
			//~ // Faz o cálculo baseado no dia atual ou usa o valor do banco, porque ao pesquisar vão aparecer propostas de fase comercial e financeiro.
			//~ $proposta["diasEmAnalise"] = $proposta["statusProposta"] === "Em análise" ? $hoje->diff($proposta["dataEnvioProposta"])->days : $proposta["diasEmAnalise"];
			//~ // Depois de realizar o cálculo formata o DateTime para uma string para mostrar na tela
			//~ $proposta["dataEnvioProposta"] = ($proposta["dataEnvioProposta"])->format("d/m/Y");
			//~ $proposta["valor"] = str_replace(".", ",", $proposta["valor"]);
		//~ }
		
		//~ return $propostas;
	//~ }

	public function cadastrarCliente(): bool
	{
		$created = $this->data->create("clientes", [
			"nome" => $_POST["nome"],
			"cpf_cnpj" => empty($_POST["cpf_cnpj"]) ? null : $_POST["cpf_cnpj"],
			"razaoSocial" => empty($_POST["razaoSocial"]) ? null : $_POST["razaoSocial"],
			"emailContato" => empty($_POST["emailContato"]) ? null : $_POST["emailContato"],
			"emailNF" => empty($_POST["emailNF"]) ? null : $_POST["emailNF"],
			"telefone" => empty($_POST["telefone"]) ? null : $_POST["telefone"],
			"endereco" => empty($_POST["endereco"]) ? null : $_POST["endereco"],
		]);

		if ($created)
		{
			$_SESSION["notification"] = [
				"message" => "Cliente cadastrado com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}

		$_SESSION["notification"] = [
			"message" => "Erro ao cadastrar cliente.",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}

	public function atualizarCliente(): bool
	{  	
		print_r($_POST);
		$affectedRows = $this->data->update("clientes", [
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

		if ($affectedRows > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Cliente atualizado com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}
		
		$_SESSION["notification"] = [
			"message" => "Erro ao atualizar cliente. Nada modificado.",
			"status" => "failure"			
		];
		//~ header("Location: ./");
		return false;
	}
	
	public function excluirCliente(): bool
	{
		$affectedRows = $this->data->delete("clientes", ["id" => $_POST["id"]]);

		if ($affectedRows > 0)
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
