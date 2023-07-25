<?php
declare(strict_types=1);

namespace App\Models;

class Post extends Model
{
	public static function tableName(): string
	{
		return 'posts';
	}

	private static string $primaryKey = 'id';
	public int $id = 0;
	public string $title = '';
	public string $text = '';

	protected static array $fields = [
		'title',
		'text',
	];

	/**
	 * @return string
	 */
	public static function getPrimaryKey(): string
	{
		return self::$primaryKey;
	}
}