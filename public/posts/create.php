<?php

use App\Models\Post;
use Carbon\Carbon;

require_once __DIR__ . '/../../vendor/autoload.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	http_response_code(404);
	include (__DIR__.'/../404.php');
}
else
{
	$errors = [];
	if(empty($_POST['title']))
	{
		$errors[] = 'Enter post title';
	}
	if(empty($_POST['text']))
	{
		$errors[] = 'Enter text';
	}
	$post = new Post();
	$post->loadData($_POST);
	$id = $post->save();

	if(empty($errors))
	{
		$post = Post::findOne(['id' => $id]);
		$response = [
			'status' => 'success',
			'message' => 'Post successfully created!',
			'post' => $post,
			'time' => Carbon::parse($post->created_at)->longRelativeToNowDiffForHumans()
		];
	}
	else {
		$response = [
			'status' => 'error',
			'errors' => $errors
		];
	}
	echo json_encode($response);
}
die();
