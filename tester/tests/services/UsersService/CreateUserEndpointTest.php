<?php

namespace services\UsersService;

class CreateUserEndpointTest extends AbstractEndToEndTest
{
	public function test_create_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('PUT', '/users', '')
		);
	}

	public function test_create_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('PUT', '/users', 'abc123')
		);
	}

	public function test_create_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":100,"text":"Email is required"}],"name":[{"codeId":104,"text":"Name is required"}],"isActive":[{"codeId":107,"text":"IsActive value is required"}]}',
			$this->makeRequest('PUT', '/users', '{}')
		);
	}

	public function test_create_endpoint_with_correct_json()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/users', '{"email":"a@b.com", "name":"abc", "isActive":true}')
		);

		$results = $this->mysqli->query('SELECT * FROM users WHERE email="a@b.com"');
		$this->assertEquals(1, $results->num_rows);

		$events = $this->getAllStorageEvents();
		$this->assertEquals('UserCreated', $events[0]['name']);
		$this->assertEquals([
			'email' => 'a@b.com',
			'name' => 'abc',
			'isActive' => true
		], $events[0]['data']);
		$this->assertEquals(['name', 'occuredAt', 'data'], array_keys($events[0]));
	}
}
