<?php

use App\Services\DatabaseConnection;

require_once __DIR__.'/vendor/autoload.php';

$db = DatabaseConnection::getInstance();
$dbConnection = $db->getConnection();

$migrations = scandir(__DIR__.'/migrations');
foreach ($migrations as $migration) {
	if ($migration === '.' || $migration === '..')
	{
		continue;
	}

	require_once __DIR__ . '/migrations/' . $migration;
	$className = pathinfo($migration, PATHINFO_FILENAME);

	$instance = new $className();
	$instance->up();
}