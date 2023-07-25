<?php

class posts_table
{
	public function up()
	{
		$db = App\Services\DatabaseConnection::getInstance();
		$query = "CREATE TABLE IF NOT EXISTS posts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                text VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )  ENGINE=INNODB;";
		$db->getConnection()->exec($query);
	}

	public function down()
	{
		$db = App\Services\DatabaseConnection::getInstance();
		$query = "DROP TABLE posts;";
		$db->getConnection()->exec($query);
	}
}