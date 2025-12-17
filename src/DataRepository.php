<?php

class DataRepository
{
	private ?object $connection = null;

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function create(string $table, array $data, ?array $password = null): array
	{
		try
		{
			if ($password)
			{
				$passwordKey = array_keys($password)[0];
				$password[$passwordKey] = password_hash($password[$passwordKey], PASSWORD_DEFAULT); 

				$data[$passwordKey] = $password[$passwordKey];
			}

			$columns = implode(", ", array_keys($data));
			$placeholders = ":" . implode(", :", array_keys($data));

			$sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

			$stmt = $this->connection->prepare($sql);
			$stmt->execute($data);
						
			return ["success" => true];
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return ["success" => false, "errorCode" => $e->getCode()];
		}
	}

	public function read(string $table, ?string $parameters = null): array
	{
		try
		{
			$sql = "SELECT * FROM $table $parameters";

			$data = $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);

			return $data;
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return [null];
		}
	}
	
	public function readJoin(string $table, string $columns, ?string $parameters = null): array
	{
		try
		{
			$sql = "SELECT $columns FROM $table JOIN $parameters";

			$data = $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);

			return $data;
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return [null];
		}
	}

	public function search(string $table, array $columns, string $keyWord, ?string $parameters = null): array
	{
		try
		{
			$likeClauses = [];

			foreach ($columns as $column)
			{
				$likeClauses[] = "$column LIKE :keyWord";
			}

			$whereClause = implode(" OR ", $likeClauses);

			$sql = "SELECT * FROM $table WHERE $whereClause $parameters";

			$stmt = $this->connection->prepare($sql);
			$stmt->bindValue(":keyWord", "%$keyWord%", PDO::PARAM_STR);
			$stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return $data;
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return [null];
		}
	}
	
	public function count(string $table, ?string $parameters = null): ?int
	{
		try
		{
			$sql = "SELECT COUNT(*) FROM $table $parameters";

			$data = $this->connection->query($sql)->fetchColumn();

			return $data;
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return null;
		}
	}
	
	public function sum(string $table, string $column, ?string $parameters = null): ?float
	{
		try
		{
			$sql = "SELECT SUM($column) FROM $table $parameters";

			$data = $this->connection->query($sql)->fetchColumn();
			$data === null ? $data = 0 : null;
			
			return $data;
		} 	
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return null;
		}
	}

	public function update(string $table, array $data, array $key): array
	{
		try
		{
			$keyName = array_keys($key)[0];
			$keyValue = $key[$keyName];
			$updateAssignments = [];

			$data[$keyName] = $keyValue;

			foreach ($data as $column => $value)
			{
				$updateAssignments[] = "$column = :$column";
			}

			$updates = implode(", ", $updateAssignments);

			$sql = "UPDATE $table SET $updates WHERE $keyName = :$keyName";

			$stmt = $this->connection->prepare($sql);
			$stmt->execute($data);

			return ["success" => true, "affectedRows" => $stmt->rowCount()];
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return ["success" => false, "errorCode" => $e->getCode()];
		}
	}

	public function delete(string $table, array $key): array
	{
		try
		{
			$keyName = array_keys($key)[0];

			$sql = "DELETE FROM $table WHERE $keyName = :$keyName";

			$stmt = $this->connection->prepare($sql);
			$stmt->execute($key);

			return ["success" => true, "affectedRows" => $stmt->rowCount()];
		}
		catch (PDOException $e)
		{
			error_log(date("Y-m-d H:i:s") . " | " . $e . "\n\n", 3, "../../errors.log");
			return ["success" => false, "errorCode" => $e->getCode()];
		}
	}
}
