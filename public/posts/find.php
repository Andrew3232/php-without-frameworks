<?php

use App\Models\Post;

require_once __DIR__ . '/../../vendor/autoload.php';

if($_SERVER['REQUEST_METHOD'] !== 'GET'){
	http_response_code(404);
	include (__DIR__.'/../404.php');
}
else
{
	$error = '';
	$post = Post::findOne(["id" => $_GET['id']]);

	if(empty($_GET['id']) || !$post)
	{
		$error = 'Post not found';
	}

	if(!$error && $post)
	{
		$response = [
			'status' => 'success',
			'post' => $post,
		];
	}
	else{
		$response = [
			'status' => 'error',
			'error' => $error
		];
	}
	echo json_encode($response);
}
die();
