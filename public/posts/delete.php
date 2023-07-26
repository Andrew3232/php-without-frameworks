<?php

use App\Models\Post;

require_once __DIR__ . '/../../vendor/autoload.php';

if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
	http_response_code(404);
	include (__DIR__.'/../404.php');
}
else
{
	$message = '';
	if(empty($_REQUEST['id']))
	{
		$message = 'Post not found';
	}

	if(!$message)
	{
		$result = Post::delete($_REQUEST['id']);
		if($result)
		{
			$response = [
				'status' => 'success',
				'message' => 'Post successfully deleted!'
			];
		}
		else{
			$response = [
				'status' => 'error',
				'message' => 'Post has been deleted!'
			];
		}
	}
	else{
		$response = [
			'status' => 'error',
			'message' => $message
		];
	}
	echo json_encode($response);
}
die();
