<?php

namespace services\AuthService;

class DefaultEndpointTest extends AbstractEndToEndTest
{
	public function test_default_endpoint()
	{
		$this->assertEquals(
			'{"type":"auth-service","config":{"rabbitmq":{"host":"events-rabbitmq","port":5672,"user":"guest","password":"guest","channel":"events"},"mysql":{"host":"auth-service-mysql","port":3306,"user":"root","password":"","database":"testdb"}},"status":{"is_connected":{"MySQL":true,"RabbitMQ":true}}}',
			$this->makeRequest('GET', '/')
		);
	}
}
