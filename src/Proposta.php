<?php

require_once "DatabaseConnection.php";
require_once "DataRepository.php";

class Proposta
{
	private ?object $connection = null;
	private ?object $data = null;
	
	public function __construct()
	{
		$this->connection = new DatabaseConnection();
		$this->data = new DataRepository($this->connection->start());
	}
	
	public function verProposta(int $id): array|false
	{
		return $this->data->read("*", "propostas", "WHERE id = $id")[0];
	}
	
	public function verPropostasEmFaseComercial(): array
	{
		$propostas = $this->data->read("propostas.*, clientes.nome AS nomeCliente", "propostas", "JOIN clientes ON propostas.idCliente = clientes.id WHERE statusProposta = 'Em análise' OR statusProposta = 'Recusada' ORDER BY dataEnvioProposta DESC");
		
		$hoje = (new DateTime())->setTime(0, 0, 0);
		
		foreach ($propostas as &$proposta)
		{
			$proposta["dataEnvioProposta"] = new DateTime($proposta["dataEnvioProposta"]);
			// Diferença de hoje caso ainda esteja em análise ou valor guardado no banco se foi aceita/recusada
			$proposta["diasEmAnalise"] = $proposta["statusProposta"] === "Em análise" ? $hoje->diff($proposta["dataEnvioProposta"])->days : $proposta["diasEmAnalise"];
			$proposta["dataEnvioProposta"] = $proposta["dataEnvioProposta"]->format("d/m/Y");
			
			foreach ($proposta as $key => $valor)
			{
				empty($valor) ? $proposta[$key] = "-" : null;
			}
			
			$proposta["valor"] = number_format($proposta["valor"], 2, ',', '.');
		}
		
		return $propostas;
	}
	
	public function verPropostasEmFaseFinanceira(): array
	{
		$propostas = $this->data->read("propostas.*, clientes.nome AS nomeCliente", "propostas", "JOIN clientes ON propostas.idCliente = clientes.id WHERE statusProposta = 'Aceita' ORDER BY dataAceiteProposta DESC;");
		
		$hoje = (new DateTime())->setTime(0, 0, 0);

		foreach ($propostas as &$proposta)
		{
			if ($proposta["dataAceiteProposta"] !== null)
			{
				$proposta["dataAceiteProposta"] = new DateTime($proposta["dataAceiteProposta"]);
				// Diferença de hoje caso ainda esteja aguardando ou valor guardado no banco se já foi recebido
				$proposta["diasAguardandoPagamento"] = $proposta["statusPagamento"] === "Aguardando" ? $hoje->diff($proposta["dataAceiteProposta"])->days : $proposta["diasAguardandoPagamento"];
				$proposta["dataAceiteProposta"] = $proposta["dataAceiteProposta"]->format("d/m/Y");
			}
			
			if ($proposta["dataPagamento"] !== null)
			{
				$proposta["dataPagamento"] = (new DateTime($proposta["dataPagamento"]))->format("d/m/Y");
			}
			
			$proposta["dataEnvioProposta"] = (new DateTime($proposta["dataEnvioProposta"]))->format("d/m/Y");
			
			foreach ($proposta as $key => $valor)
			{
				empty($valor) ? $proposta[$key] = "-" : null;
			}
			
			$proposta["valor"] = number_format($proposta["valor"], 2, ',', '.');
		}
		
		return $propostas;
	}

	public function pesquisarProposta(): array
	{
		$propostas = $this->data->search("propostas.*, clientes.nome AS nomeCliente", "propostas", [
			"numeroProposta",
			"statusPagamento",
			"valor",
	    		"idCliente",
			"observacoes",
	    	], $_GET["q"], "JOIN clientes ON propostas.idCliente = clientes.id", "ORDER BY dataEnvioProposta DESC;");
		
		$hoje = (new DateTime())->setTime(0, 0, 0);
		
		foreach ($propostas as &$proposta)
		{
			if ($proposta["dataAceiteProposta"] !== null)
			{
				$proposta["dataAceiteProposta"] = new DateTime($proposta["dataAceiteProposta"]);
				// Diferença de hoje caso ainda esteja aguardando ou valor guardado no banco se já foi recebido
				$proposta["diasAguardandoPagamento"] = $proposta["statusPagamento"] === "Aguardando" ? $hoje->diff($proposta["dataAceiteProposta"])->days : $proposta["diasAguardandoPagamento"];
				$proposta["dataAceiteProposta"] = $proposta["dataAceiteProposta"]->format("d/m/Y");
			}
			
			$proposta["dataEnvioProposta"] = new DateTime($proposta["dataEnvioProposta"]);
			$proposta["diasEmAnalise"] = $proposta["statusProposta"] === "Em análise" ? $hoje->diff($proposta["dataEnvioProposta"])->days : $proposta["diasEmAnalise"];
			$proposta["dataEnvioProposta"] = ($proposta["dataEnvioProposta"])->format("d/m/Y");
			
			if ($proposta["dataPagamento"] !== null)
			{
				$proposta["dataPagamento"] = (new DateTime($proposta["dataPagamento"]))->format("d/m/Y");
			}
			
			foreach($proposta as $key => $valor)
			{
				empty($valor) ? $proposta[$key] = "-" : null;
			}
			 
			$proposta["valor"] = number_format($proposta["valor"], 2, ',', '.');
		}
		
		return $propostas;
	}

	public function cadastrarProposta(): bool
	{
		$create = $this->data->create("propostas", [
			"numeroProposta" => empty($_POST["numeroProposta"]) ? null : $_POST["numeroProposta"],
			"dataEnvioProposta" => $_POST["dataEnvioProposta"],
			"valor" => str_replace(",", ".", $_POST["valor"]),
			"cliente" => $_POST["cliente"],
			"observacoes" => $_POST["observacoes"],
		]);
		
		if ($create["success"] === true)
		{
			$_SESSION["notification"] = [
				"message" => "Proposta cadastrada com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}

		$_SESSION["notification"] = [
			"message" => "Erro ao cadastrar proposta. Cód: {$create['errorCode']}",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}

	public function atualizarStatusProposta(): bool
	{  
		if (!empty($_POST["dataPagamento"])) // Acontece toda atualização com dataPagamento mesmo já tendo diasAguardandoPagamento no banco 
		{
			if (!$dataAceiteProposta = DateTime::createFromFormat("d/m/Y", $_POST["dataAceiteProposta"]))
			{
				$_SESSION["notification"] = [
					"message" => "Essa proposta ainda não foi aceita para ter uma data de pagamento! Nada modificado!",
					"status" => "failure"		
				];
				header("Location: ./");
				return false;
			}

			$dataPagamento = DateTime::createFromFormat("Y-m-d", $_POST["dataPagamento"]);
			$_POST["diasAguardandoPagamento"] = (($dataPagamento->setTime(0, 0, 0))->diff($dataAceiteProposta->setTime(0, 0, 0)))->days;
		}		

		$update = $this->data->update("propostas", [
				"numeroProposta" => empty($_POST["numeroProposta"]) ? null : $_POST["numeroProposta"],
				"cliente" => $_POST["cliente"],
				"valor" => str_replace(",", ".", $_POST["valor"]),
				"dataPagamento" => empty($_POST["dataPagamento"]) ? null : $_POST["dataPagamento"],
				"statusPagamento" => empty($_POST["dataPagamento"]) ? "Aguardando" : "Recebido",
				"formaPagamento" => $_POST["formaPagamento"],
				"observacoes" => $_POST["observacoes"],
				"diasAguardandoPagamento" => $_POST["diasAguardandoPagamento"],
			],
			[
				"id" => $_POST["id"]
			]
		);

		if ($update["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Status da Proposta atualizado com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}
		
		$_SESSION["notification"] = [
			"message" => "Erro ao atualizar Status da Proposta. Nada modificado. Cód: {$update['errorCode']}",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}

	public function aceitarProposta(): bool
	{
		$hoje = (new DateTime())->setTime(0, 0, 0);
		$diasEmAnalise = ($hoje->diff((DateTime::createFromFormat("d/m/Y", $_POST["dataEnvioProposta"]))->setTime(0, 0, 0)))->days;

		$update = $this->data->update("propostas", [
				"statusProposta" => "Aceita",
				"statusPagamento" => "Aguardando", // Para caso ela tenha sido recusada e depois aceita
				"dataAceiteProposta" => $hoje->format("Y-m-d"), 
				"diasEmAnalise" => $diasEmAnalise
			], 
			[
				"id" => $_POST["id"]
			]);

		if ($update["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Proposta aceita com sucesso. Movida para Financeiro.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}
		
		$_SESSION["notification"] = [
			"message" => "Erro ao aceitar proposta. Nada modificado.",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}

	public function voltarEmAnalise(): bool
	{
		$update = $this->data->update("propostas", [
			"statusProposta" => "Em análise",
		], 
		[
			"id" => $_POST["id"]
		]);

		if ($update["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Proposta retornada para em análise com sucesso. Movida para Comercial.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}

		$_SESSION["notification"] = [
			"message" => "Erro ao retornar proposta para em análise. Nada modificado.",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}
	
	public function recusarProposta(): bool
	{
		$hoje = (new DateTime())->setTime(0, 0, 0);
		$diasEmAnalise = ($hoje->diff((DateTime::createFromFormat("d/m/Y", $_POST["dataEnvioProposta"]))->setTime(0, 0, 0)))->days;
		
		$update = $this->data->update("propostas", [
			"statusProposta" => "Recusada",
			"statusPagamento" => "Recusada",
			"diasEmAnalise" => $diasEmAnalise
		], 
		[
			"id" => $_POST["id"]
		]);

		if ($update["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Proposta recusada com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}

		$_SESSION["notification"] = [
			"message" => "Erro ao recusar proposta. Nada modificado.",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;

	}
	
	public function excluirProposta(): bool
	{
		$delete = $this->data->delete("propostas", ["id" => $_POST["id"]]);

		if ($delete["affectedRows"] > 0)
		{
			$_SESSION["notification"] = [
				"message" => "Proposta excluída com sucesso.",
				"status" => "success"			
			];
			header("Location: ./");
			return true;
		}
		
		$_SESSION["notification"] = [
			"message" => "Erro ao excluir proposta. Nada modificado.",
			"status" => "failure"			
		];
		header("Location: ./");
		return false;
	}
	
	public function gerarRelatorio(string $data): array|bool
	{
		$data = new DateTime($data);
		$mes = $data->format("m");
		$ano = $data->format("Y");

		$propostasEnviadas = $this->data->count("propostas", "WHERE MONTH(dataEnvioProposta) = $mes AND YEAR(dataEnvioProposta) = $ano");
		$propostasAceitas = $this->data->count("propostas", "WHERE MONTH(dataAceiteProposta) = $mes AND YEAR(dataAceiteProposta) = $ano");
		$valorRecebido = $this->data->sum("propostas", "valor", "WHERE (MONTH(dataAceiteProposta) = $mes AND YEAR(dataAceiteProposta) = $ano) AND statusPagamento = 'Recebido'");
		$valorTotal = $this->data->sum("propostas", "valor", "WHERE (MONTH(dataAceiteProposta) = $mes AND YEAR(dataAceiteProposta) = $ano) AND statusProposta = 'Aceita'");
		
		return [
			"data" => "$mes/$ano",
			"propostasEnviadas" => $propostasEnviadas,
			"propostasAceitas" => $propostasAceitas,
			"valorRecebido" => number_format($valorRecebido, 2, ',', '.'),
			"valorTotal" => number_format($valorTotal, 2, ',', '.')
		];
	}
}
