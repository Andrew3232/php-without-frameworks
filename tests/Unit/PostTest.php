<?php

namespace Tests\Unit;

use App\Models\Model;
use App\Models\Post;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
	private object $post;
	private string $title;
	private string $text;
	private Client $client;

	protected function setUp(): void
	{
		parent::setUp();

		$this->post = new Post();
		$this->post->title = 'Title';
		$this->post->text = 'Test post';
		$this->post->id = $this->post->save();
		$this->title = 'New post';
		$this->text = 'Awesome description for this post';
		$this->client = new Client(['base_uri' =>'http://host.docker.internal:8080']);
	}

	protected function tearDown(): void
	{
		parent::tearDown();

		Post::delete($this->post->id);
	}

	public function test_inherit_base_model()
	{
		$this->assertInstanceOf(Model::class, $this->post);
	}

	public function test_exist_properties_such_as_fields()
	{
		$fields = Post::getFields();
		foreach($fields as $field){
			$this->assertObjectHasProperty($field, $this->post);
		}
	}

	public function test_get_posts()
	{
		$posts = Post::all();
		$this->assertInstanceOf(Post::class, current($posts));
		$this->assertIsArray($posts);
	}

	public function test_posts_order_by()
	{
		$posts = Post::all('id','DESC');
		$this->assertGreaterThanOrEqual(end($posts)->id, current($posts)->id);
	}

	public function test_get_post()
	{
		$post = Post::findOne(['id'=> $this->post->id]);
		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals($this->post->id, $post->id);
		$this->assertEquals($this->post->title, $post->title);
		$this->assertEquals($this->post->text, $post->text);
	}

	public function test_create_post_successful(): void
	{
		$data = [
			'title' => $this->title,
			'text' => $this->text,
		];

		$response = $this->client->post('/posts/create.php', ['form_params' => $data]);
		$responseData = json_decode($response->getBody()->getContents());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertObjectNotHasProperty('error', $responseData);
		$this->assertObjectNotHasProperty('errors', $responseData);
		$this->assertObjectHasProperty('post', $responseData);
		$this->assertEquals('success', $responseData->status);
		$this->assertEquals($this->title, $responseData->post->title);
		$this->assertEquals($this->text, $responseData->post->text);
	}

	public function test_create_post_validation(): void
	{
		$response = $this->client->post('/posts/create.php');
		$responseData = json_decode($response->getBody()->getContents());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertObjectNotHasProperty('post', $responseData);
		$this->assertObjectHasProperty('errors', $responseData);
		$this->assertEquals('error', $responseData->status);
		$this->assertTrue(in_array('Enter post title', $responseData->errors));
		$this->assertTrue(in_array('Enter text', $responseData->errors));
	}

	public function test_update_post_successful(): void
	{
		$data = [
			'id' => $this->post->id,
			'title' => 'update title',
			'text' => 'new text',
		];

		$response = $this->client->put('/posts/update.php?'. http_build_query($data));
		$responseData = json_decode($response->getBody()->getContents());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertObjectNotHasProperty('errors', $responseData);
		$this->assertObjectHasProperty('post', $responseData);
		$this->assertEquals('success', $responseData->status);
		$this->assertEquals($data['title'], $responseData->post->title);
		$this->assertEquals($data['text'], $responseData->post->text);
	}

	public function test_update_post_validation(): void
	{
		$response = $this->client->put('/posts/update.php?id='. $this->post->id);
		$responseData = json_decode($response->getBody()->getContents());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertObjectNotHasProperty('post', $responseData);
		$this->assertObjectHasProperty('errors', $responseData);
		$this->assertEquals('error', $responseData->status);
		$this->assertTrue(in_array('Enter post title', $responseData->errors));
		$this->assertTrue(in_array('Enter text', $responseData->errors));
	}

	public function test_delete_post_successful(): void
	{
		$response = $this->client->delete('/posts/delete.php?id=' . $this->post->id);
		$responseData = json_decode($response->getBody()->getContents());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertObjectHasProperty('message', $responseData);
		$this->assertEquals('success', $responseData->status);
		$this->assertEquals('Post successfully deleted!', $responseData->message);
	}

	public function test_delete_post_validation(): void
	{
		$response = $this->client->delete('/posts/delete.php');
		$responseData = json_decode($response->getBody()->getContents());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('error', $responseData->status);
		$this->assertEquals('Post not found', $responseData->message);
	}
}
