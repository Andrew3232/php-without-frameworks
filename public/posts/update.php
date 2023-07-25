<?php

use App\Models\Post;
use Carbon\Carbon;

require_once __DIR__ . '/../../vendor/autoload.php';

if($_SERVER['REQUEST_METHOD'] !== 'PUT' || empty($_REQUEST['id'])){
	http_response_code(404);
	include (__DIR__.'/../404.php');
}
else
{
	$id = $_REQUEST['id'];
	$errors = [];
	if(empty($_REQUEST['title']))
	{
		$errors[] = 'Enter post title';
	}
	if(empty($_REQUEST['text']))
	{
		$errors[] = 'Enter text';
	}

	$post = Post::findOne(['id' => $id]);
	$result = $post->update($_REQUEST);

	if(empty($errors) || $result)
	{
		$post = Post::findOne(['id' => $id]);
		$response = [
			'status' => 'success',
			'message' => 'Post successfully updated!',
			'post' => $post,
			'time' => Carbon::parse($post->created_at)->longRelativeToNowDiffForHumans()
		];
	}
	else{
		$response = [
			'status' => 'error',
			'errors' => $errors
		];
	}
	echo json_encode($response);
}
die();
