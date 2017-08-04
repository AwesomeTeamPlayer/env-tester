<?php

namespace services\AuthService;

class LogoutEndpointTest extends AbstractEndToEndTest
{
	public function test_logout_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/logout', '')
		);
	}

	public function test_logout_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/logout', 'abc123')
		);
	}

	public function test_logout_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","sessionId":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/logout', '{}')
		);
	}

	public function test_logout_endpoint_with_empty_sessionId()
	{
		$this->assertEquals(
			'{"status":"failed","sessionId":[{"codeId":105,"text":"SessionId value can not be empty"}]}',
			$this->makeRequest('POST', '/logout', '{"sessionId":""}')
		);
	}

	public function test_logout_endpoint_when_sessionId_is_not_correct()
	{
		$this->assertEquals(
			'{"status":"failed"}',
			$this->makeRequest('POST', '/logout', '{"sessionId":"abc123"}')
		);
	}

	public function test_logout_endpoint_when_everything_is_correct()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$result = $this->makeRequest('POST', '/login', '{"login":"login", "password":"password"}');
		$sessionId = json_decode($result, true)['sessionId'];

		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('POST', '/logout', '{"sessionId":"' . $sessionId . '"}')
		);
	}

}
