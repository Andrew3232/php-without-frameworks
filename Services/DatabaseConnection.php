<?php

namespace App\Services;

final class DatabaseConnection
{
	private static object|null $instance = null;
	private \PDO $connection;

	private string $host = 'db';
	private string $user = 'user';
	private string $password = 'secret';
	private string $database = 'php-app';

	private function __construct()
	{
		$this->connection = new \PDO("mysql:host=$this->host;port=3306;dbname=$this->database",
			$this->user,
			$this->password,
		);

		$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
	}

	/**
	 * @return DatabaseConnection
	 */
	public static function getInstance(): DatabaseConnection
	{
		if(!self::$instance)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return \PDO
	 */
	public function getConnection(): \PDO
	{
		return $this->connection;
	}
}