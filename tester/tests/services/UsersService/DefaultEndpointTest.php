<?php

namespace services\UsersService;

class DefaultEndpointTest extends AbstractEndToEndTest
{
	public function test_default_endpoint()
	{
		$this->assertEquals(
			'{"type":"users-service","config":{"rabbitmq":{"host":"events-rabbitmq","port":5672,"user":"guest","password":"guest","channel":"events"},"mysql":{"host":"users-service-mysql","port":3306,"user":"root","password":"","database":"testdb"},"name":{"minLength":3,"maxLength":255}},"status":{"is_connected":{"MySQL":true,"RabbitMQ":true}}}',
			$this->makeRequest('GET', '/')
		);
	}
}
