<?php
declare(strict_types=1);

namespace App\Models;

use App\Services\DatabaseConnection;

abstract class Model
{
	abstract public static function tableName(): string;
	private static string $primaryKey = 'id';

	protected static array $fields = [];

	/**
	 * @return string
	 */
	public static function getPrimaryKey(): string
	{
		return self::$primaryKey;
	}

	public function loadData(array $data): void
	{
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		return static::$fields;
	}

	/**
	 * @param $sql
	 *
	 * @return \PDOStatement
	 */
	public static function prepare($sql): \PDOStatement
	{
		return DatabaseConnection::getInstance()->getConnection()->prepare($sql);
	}

	/**
	 * @param  string  $oderBy
	 * @param  string  $direction
	 *
	 * @return mixed
	 */
	public static function all(string $oderBy = 'id', string $direction = 'ASC'): mixed
	{
		$tableName = static::tableName();
		$sql = "SELECT * FROM $tableName ORDER BY $oderBy $direction";
		$statement = self::prepare($sql);
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, static::class);
		$statement->execute();

		return $statement->fetchAll();
	}

	/**
	 * @param  array   $where
	 * @param  string  $oderBy
	 * @param  string  $direction
	 *
	 * @return mixed
	 */
	public static function find(array $where = [], string $oderBy = 'id', string $direction = 'ASC'): mixed
	{
		$tableName = static::tableName();
		$columns = array_keys($where);
		$whereQuery = $where ? 'WHERE '.implode("AND", array_map(fn($column) => "$column = :$column", $columns)) : '';
		$statement = self::prepare("SELECT * FROM $tableName $whereQuery ORDER By $oderBy $direction");
		foreach ($where as $key => $item) {
			$statement->bindValue(":$key", $item);
		}
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, static::class);
		$statement->execute();

		return $statement->fetchAll();
	}

	/**
	 * @param  array   $where
	 * @param  int     $page
	 * @param  int     $perPage
	 * @param  string  $oderBy
	 * @param  string  $direction
	 *
	 * @return mixed
	 */
	public static function findWithPagination(array $where = [], int $page = 1,int $perPage = 10, string $oderBy = 'id', string $direction = 'ASC'): mixed
	{
		$tableName = static::tableName();
		$columns = array_keys($where);
		$whereQuery = implode("AND", array_map(fn($column) => "$column = :$column", $columns));
		$offset = ($page - 1) * $perPage;
		$statement = self::prepare("SELECT * FROM $tableName WHERE id > $offset AND $whereQuery ORDER By $oderBy $direction LIMIT $perPage");
		foreach ($where as $key => $item) {
			$statement->bindValue(":$key", $item);
		}
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, static::class);
		$statement->execute();

		return $statement->fetchAll();
	}

	/**
	 * @param  array  $where
	 *
	 * @return mixed
	 */
	public static function findOne(array $where = []): mixed
	{
		$tableName = static::tableName();
		$columns = array_keys($where);
		$whereQuery = $where ? 'WHERE '.implode("AND", array_map(fn($column) => "$column = :$column", $columns)) : '';
		$statement = self::prepare("SELECT * FROM $tableName $whereQuery");
		foreach ($where as $key => $item) {
			$statement->bindParam(":$key", $item);
		}
		$statement->execute();

		return $statement->fetchObject(static::class);
	}

	/**
	 * @return false|string
	 */
	public function save(): false|string
	{
		$tableName = static::tableName();
		$fields = static::getFields();
		$params = array_map(fn($field) => ":$field", $fields);
		$statement = self::prepare("INSERT INTO $tableName (" . implode(", ", $fields) . ") VALUES (" . implode(",", $params) . ")");
		foreach ($fields as $field) {
			$statement->bindValue(":$field", $this->{$field});
		}
		$statement->execute();

		return DatabaseConnection::getInstance()->getConnection()->lastInsertId();
	}

	/**
	 * @param  array  $values
	 *
	 * @return bool
	 */
	public function update(array $values): bool
	{
		$tableName = static::tableName();
		$fields = static::getFields();
		$params = array_map(fn($field) => "$field=:$field", $fields);
		$statement = self::prepare("UPDATE $tableName SET " . implode(", ", $params) . " WHERE id=:id");
		$columns = array_intersect_key($values, array_flip($fields));
		$columns['id'] = $this->id;

		return $statement->execute($columns);
	}

	/**
	 * @param  array|string  $id
	 *
	 * @return bool
	 */
	public static function delete(array|string $id): bool
	{
		$tableName = static::tableName();
		if(is_array($id))
			$id = implode(',', $id);
		$sql = "DELETE FROM $tableName WHERE id=?";
		$statement = self::prepare($sql);
		$statement->execute([$id]);

		return $statement->rowCount() > 0;
	}
}