<?php

namespace services\UsersService;

class GetUserEndpointTest extends AbstractEndToEndTest
{
	public function test_get_user_endpoint_without_email()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":100,"text":"Email is required"}]}',
			$this->makeRequest('GET', '/users', '')
		);
	}

	public function test_get_user_endpoint_with_incorrect_email()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":101,"text":"Given email address is incorrect"}]}',
			$this->makeRequest('GET', '/users?email=abc', '')
		);
	}

	public function test_get_user_endpoint_with_not_existing_email()
	{
		$this->assertEquals(
			'{"status":"failed","email":[{"codeId":103,"text":"Given email address does not exists"}]}',
			$this->makeRequest('GET', '/users?email=john%40domain.com', '')
		);
	}

	public function test_get_user_endpoint_when_email_exists()
	{
		$this->makeRequest('PUT', '/users', '{"email":"john@domain.com", "name":"abc", "isActive":true}');

		$this->assertEquals(
			'{"status":"success","name":"abc","email":"john@domain.com","isActive":true}',
			$this->makeRequest('GET', '/users?email=john%40domain.com', '')
		);
	}

}
