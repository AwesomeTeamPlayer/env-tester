<?php

namespace services\AuthService;

class CheckLoginEndpointTest extends AbstractEndToEndTest
{
	public function test_has_login_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/has-login', '')
		);
	}

	public function test_has_login_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/has-login', 'abc123')
		);
	}

	public function test_has_login_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/has-login', '{}')
		);
	}

	public function test_has_login_endpoint_with_empty_login()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":102,"text":"The login can not be empty"}]}',
			$this->makeRequest('POST', '/has-login', '{"login":""}')
		);
	}

	public function test_has_login_endpoint_with_correct_login_when_login_does_not_exist()
	{
		$this->assertEquals(
			'{"hasLogin":false}',
			$this->makeRequest('POST', '/has-login', '{"login":"abc123"}')
		);
	}

	public function test_has_login_endpoint_with_correct_login_when_login_exists()
	{
		$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}');

		$this->assertEquals(
			'{"hasLogin":true}',
			$this->makeRequest('POST', '/has-login', '{"login":"login"}')
		);
	}
}
