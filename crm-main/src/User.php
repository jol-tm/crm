<?php

require_once 'DatabaseConnection.php';

class User
{
	private ?object $connection = null;
	private ?object $data = null;
	
	public function __construct()
	{
		$this->connection = (new DatabaseConnection())->start();
	}

	public function authenticate(string $table, array $email, array $password): bool
	{
		try
		{
			$emailKey = array_keys($email)[0];
			$passwordKey = array_keys($password)[0];

			$sql = "SELECT $passwordKey FROM $table WHERE $emailKey = :$emailKey";

			$stmt = $this->connection->prepare($sql);
			$stmt->bindParam(":email", $email[$emailKey], PDO::PARAM_STR);

			$stmt->execute();

			$hash = $stmt->fetchColumn();

			if (password_verify($password[$passwordKey], $hash))
			{
				session_regenerate_id(true);
				$_SESSION['authenticatedUser'] = $email[$emailKey];
				return true;
			}

			return false;
		}
		catch (PDOException $e)
		{
			error_log("\n\n" . date("Y-m-d H:i:s") . " | " . $e, 3, "../../errors.log");
			return false;
		}
	}

	public function disconnect(): bool
	{
		$params = session_get_cookie_params();
		
		setcookie(
			session_name(), '', time() - 86400,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
		
		session_unset();
		return session_destroy();
	}
}
