<?php

namespace services\AuthService;

class UpdatePairEndpointTest extends AbstractEndToEndTest
{
	public function test_update_pair_endpoint_with_empty_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/pair', '')
		);
	}

	public function test_update_pair_endpoint_with_incorrect_body()
	{
		$this->assertEquals(
			'{"status":"failed","json":[{"codeId":99,"text":"Json is incorrect"}]}',
			$this->makeRequest('POST', '/pair', 'abc123')
		);
	}

	public function test_update_pair_endpoint_with_empty_json()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}],"password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/pair', '{}')
		);
	}

	public function test_update_pair_endpoint_with_empty_login_and_without_password()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":102,"text":"The login can not be empty"}],"password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/pair', '{"login":""}')
		);
	}

	public function test_update_pair_endpoint_with_empty_password()
	{
		$this->assertEquals(
			'{"status":"failed","password":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/pair', '{"login":"abc"}')
		);
	}

	public function test_update_pair_endpoint_without_login_and_with_empty_password()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":104,"text":"This value is required"}]}',
			$this->makeRequest('POST', '/pair', '{"password":""}')
		);
	}

	public function test_update_pair_when_pair_does_not_exist()
	{
		$this->assertEquals(
			'{"status":"failed","login":[{"codeId":101,"text":"Given Login does not exist in the database"}]}',
			$this->makeRequest('POST', '/pair', '{"login":"login", "password":"password"}')
		);
	}

	public function test_update_pair_when_pair_exists()
	{
		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('PUT', '/pair', '{"login":"login", "password":"password"}')
		);

		$this->assertEquals(
			'{"status":"success"}',
			$this->makeRequest('POST', '/pair', '{"login":"login", "password":"password123"}')
		);
	}
}
