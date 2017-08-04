<?php

namespace services\AuthService;

class CreatePairEndpointTest extends AbstractEndToEndTest
{
	public function test_create_pair_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('PUT', '/pair', '')
		);
	}

	public function test_create_pair_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('PUT', '/pair', 'abc123')
		);
	}

	public function test_create_pair_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}],"password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('PUT', '/pair', '{}')
		);
	}

	public function test_create_pair_endpoint_with_empty_login_and_without_password()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":102,"text":"The login can not be empty"}],"password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('PUT', '/pair', '{"login":""}')
		);
	}

	public function test_create_pair_endpoint_with_empty_password()
	{
		$this->assertEquals(
			'{"status":"failed","password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('PUT', '/pair', '{"login":"abc"}')
		);
	}

	public function test_create_pair_endpoint_without_login_and_with_empty_password()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('PUT', '/pair', '{"password":""}')
		);
	}

	public function test_create_pair_endpoint_with_login_password()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"hasLogin":true}',
			$this->makeRequest('POST', '/has-login', '{"login":"login"}')
		);
	}

	public function test_create_pair_endpoint_double_requests()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"hasLogin":true}',
			$this->makeRequest('POST', '/has-login', '{"login":"login"}')
		);

		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":100,"text":"Given Login already exists in the database"}]}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"hasLogin":true}',
			$this->makeRequest('POST', '/has-login', '{"login":"login"}')
		);
	}
}
