<?php

namespace services\AuthService;

class LoginEndpointTest extends AbstractEndToEndTest
{
	public function test_login_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/login', '')
		);
	}

	public function test_login_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/login', 'abc123')
		);
	}

	public function test_login_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}],"password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/login', '{}')
		);
	}

	public function test_login_endpoint_with_empty_login_without_password()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":102,"text":"The login can not be empty"}],"password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/login', '{"login":""}')
		);
	}

	public function test_login_endpoint_with_empty_password_without_login()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/login', '{"password":""}')
		);
	}

	public function test_login_endpoint_with_empty_database()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":101,"text":"Given Login does not exist in the database"}]}',
			$this->makeRequest('POST', '/login', '{"login":"login", "password":"password"}')
		);
	}

	public function test_login_endpoint_when_password_is_incorrect()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"status":"failed","password":[{"codeId":103,"text":"Given password is incorrect"}]}',
			$this->makeRequest('POST', '/login', '{"login":"login", "password":"password123"}')
		);
	}

	public function test_login_endpoint_when_login_is_incorrect()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":101,"text":"Given Login does not exist in the database"}]}',
			$this->makeRequest('POST', '/login', '{"login":"login123", "password":"password"}')
		);
	}

	public function test_login_endpoint_when_login_and_password_are_incorrect()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":101,"text":"Given Login does not exist in the database"}]}',
			$this->makeRequest('POST', '/login', '{"login":"login123", "password":"password123"}')
		);
	}

	public function test_login_endpoint_when_login_and_password_are_correct()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$response = json_decode($this->makeRequest('POST', '/login', '{"login":"login", "password":"password"}'), true);
		$this->assertEquals('success', $response['status']);
		$this->assertTrue(strlen($response['sessionId']) > 10);

		$events = $this->getAllStorageEvents();
		$this->assertCount(1, $events);

		$event = $events[0];

		unset($event['occuredAt']);
		unset($event['data']['sessionId']);

		$this->assertEquals(
			[
				'name' => 'LoggedUser',
				'data' => [
					'login' => 'login',
				]
			],
			$event
		);
	}
}
