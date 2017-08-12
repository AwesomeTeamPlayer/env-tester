<?php

namespace services\UsersService;

class UpdateUserEndpointTest extends AbstractEndToEndTest
{
	public function test_update_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/users', '')
		);
	}

	public function test_update_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/users', 'abc123')
		);
	}

	public function test_update_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":100,"text":"Email is required"}],"name":[{"codeId":104,"text":"Name is required"}],"isActive":[{"codeId":107,"text":"IsActive value is required"}]}',
			$this->makeRequest('POST', '/users', '{}')
		);
	}

	public function test_update_endpoint_with_empty_email()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":101,"text":"Given email address is incorrect"}],"name":[{"codeId":104,"text":"Name is required"}],"isActive":[{"codeId":107,"text":"IsActive value is required"}]}',
			$this->makeRequest('POST', '/users', '{"email":""}')
		);
	}

	public function test_update_endpoint_with_incorrect_email()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":101,"text":"Given email address is incorrect"}],"name":[{"codeId":104,"text":"Name is required"}],"isActive":[{"codeId":107,"text":"IsActive value is required"}]}',
			$this->makeRequest('POST', '/users', '{"email":"aaa"}')
		);
	}

	public function test_update_endpoint_with_too_short_name()
	{
		$this->assertEquals(
			'{"status":"failed","name":[{"codeId":105,"text":"Name is too short (minimal length is 3)"}],"isActive":[{"codeId":107,"text":"IsActive value is required"}]}',
			$this->makeRequest('POST', '/users', '{"email":"john@domain.com", "name":"a"}')
		);
	}

	public function test_update_endpoint_with_too_long_name()
	{
		$this->assertEquals(
			'{"status":"failed","name":[{"codeId":106,"text":"Name is too long (maximal length is 255)"}],"isActive":[{"codeId":107,"text":"IsActive value is required"}]}',
			$this->makeRequest('POST', '/users', '{"email":"john@domain.com", "name":"' . $this->generateString(300) . '"}')
		);
	}

	public function test_update_endpoint_with_no_boolean_isActive_value()
	{
		$this->assertEquals(
			'{"status":"failed","isActive":[{"codeId":108,"text":"IsActive value has to be boolean value"}]}',
			$this->makeRequest('POST', '/users', '{"email":"john@domain.com", "name":"John", "isActive":"abc123"}')
		);
	}

	private function generateString(int $length) : string
	{
		$string = '';

		for ($i = 0; $i < $length; $i++)
		{
			$string .= 'a';
		}

		return $string;
	}

	public function test_update_endpoint_with_correct_json()
	{
		$this->makeRequest('PUT', '/users', '{"email":"a@b.com", "name":"abc", "isActive":true}');

		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('POST', '/users', '{"email":"a@b.com", "name":"Xyz", "isActive":false}')
		);

		$results = $this->mysqli->query('SELECT * FROM users WHERE email="a@b.com"');
		$this->assertEquals(1, $results->num_rows);

		$rows = $results->fetch_array(MYSQLI_ASSOC);
		$this->assertEquals([
			'email' => 'a@b.com',
			'name' => 'Xyz',
			'is_active' => '0',
		], $rows);

		$events = $this->getAllStorageEvents();

		$this->assertEquals('UserCreated', $events[0]['name']);
		$this->assertEquals([
			'email' => 'a@b.com',
			'name' => 'abc',
			'isActive' => true
		], $events[0]['data']);
		$this->assertEquals(['name', 'occuredAt', 'data'], array_keys($events[0]));

		$this->assertEquals('UserUpdated', $events[1]['name']);
		$this->assertEquals([
			'email' => 'a@b.com',
			'name' => 'Xyz',
			'isActive' => false
		], $events[1]['data']);
		$this->assertEquals(['name', 'occuredAt', 'data'], array_keys($events[1]));
	}

	public function test_update_endpoint_when_user_does_not_exists()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":103,"text":"Given email address does not exists"}]}',
			$this->makeRequest('POST', '/users', '{"email":"a@b.com", "name":"abc", "isActive":true}')
		);

		$results = $this->mysqli->query('SELECT * FROM users WHERE email="a@b.com"');
		$this->assertEquals(0, $results->num_rows);

		$events = $this->getAllStorageEvents();
		$this->assertCount(0, $events);
	}
}
