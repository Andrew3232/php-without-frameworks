<?php

namespace Tests\Unit;

use App\Services\DatabaseConnection;
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
	}

	/**
	 * @return void
	 */
	public function test_is_singleton(): void
	{
		$db = DatabaseConnection::getInstance();
		$this->assertInstanceOf(DatabaseConnection::class, $db);
		$this->assertTrue(method_exists(DatabaseConnection::class, 'getInstance'));
		$this->assertObjectHasProperty('instance', $db);
	}

	/**
	 * @return void
	 */
	public function test_connect(): void
	{
		$this->assertInstanceOf(\PDO::class, DatabaseConnection::getInstance()->getConnection());
	}
}
