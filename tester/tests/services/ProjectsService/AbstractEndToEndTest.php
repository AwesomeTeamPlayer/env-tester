<?php

namespace services\ProjectsService;

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

	public function setUp()
	{
		$this->mysqli = new mysqli(
			getenv('PROJECTS_SERVICE_MYSQL_HOST'),
			getenv('PROJECTS_SERVICE_MYSQL_LOGIN'),
			getenv('PROJECTS_SERVICE_MYSQL_PASSWORD'),
			getenv('PROJECTS_SERVICE_MYSQL_DATABASE'),
			getenv('PROJECTS_SERVICE_MYSQL_PORT')
		);

		$this->setUpEventsQueue();
	}

	public function tearDown()
	{
		$this->mysqli->query('TRUNCATE TABLE projects_users;');
		$this->mysqli->close();

		$this->tearDownEventsQueue();
	}

	protected function makeRequest($method, $path, $bodyContent = '') : string
	{
		$url = 'http://projects-service-nginx';

		$client = new Client();
		$response = $client->send(
			new Request($method, $url . $path, [], $bodyContent)
		);

		return (string) $response->getBody();
	}
}
