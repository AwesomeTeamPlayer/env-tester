<?php

namespace services\ProjectsService;

class DefaultEndpointTest extends AbstractEndToEndTest
{
	public function test_default_endpoint()
	{
		$this->assertEquals(
			'{"type":"projects-service","config":{"rabbitmq":{"host":"projects-service-rabbitmq","port":5672,"user":"guest","password":"guest","channel":"events"},"mysql":{"host":"projects-service-mysql","port":3306,"user":"root","password":"","database":"testdb"}},"status":{"is_connected":{"MySQL":true,"RabbitMQ":true}}}',
			$this->makeRequest('GET', '/')
		);
	}

	public function test_failed()
	{
		$this->assertTrue(false);
	}
}
