<?php

namespace services\AuthService;

use AbstractTest;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use mysqli;
use Slim\App;

abstract class AbstractEndToEndTest extends AbstractTest
{
	/**
	 * @var string
	 */
	const QUEUE_NAME = 'events';

	/**
	 * @var mysqli
	 */
	protected $mysqli;

	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @return string
	 */
	protected function getQueueName() : string
	{
		return self::QUEUE_NAME;
	}

	public function setUp()
	{
		$this->mysqli = new mysqli(
			getenv('AUTH_SERVICE_MYSQL_HOST'),
			getenv('AUTH_SERVICE_MYSQL_LOGIN'),
			getenv('AUTH_SERVICE_MYSQL_PASSWORD'),
			getenv('AUTH_SERVICE_MYSQL_DATABASE'),
			getenv('AUTH_SERVICE_MYSQL_PORT')
		);

		$this->setUpEventsQueue();
	}

	public function tearDown()
	{
		$this->mysqli->query('TRUNCATE TABLE login_password;');
		$this->mysqli->query('TRUNCATE TABLE login_session;');
		$this->mysqli->close();

		$this->tearDownEventsQueue();
	}

	protected function makeRequest($method, $path, $bodyContent = '') : string
	{
		$url = 'http://auth-service-nginx';

		$client = new Client();
		$response = $client->send(
			new Request($method, $url . $path, [], $bodyContent)
		);

		return (string) $response->getBody();
	}

}
