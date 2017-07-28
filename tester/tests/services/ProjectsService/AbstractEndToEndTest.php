<?php

namespace services\ProjectsService;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use mysqli;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;
use Slim\App;

abstract class AbstractEndToEndTest extends TestCase
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
	 * @var AMQPStreamConnection
	 */
	protected $connection;

	/**
	 * @var AMQPChannel
	 */
	protected $channel;

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

		$this->connection = new AMQPStreamConnection(
			getenv('PROJECTS_SERVICE_RABBIT_HOST'),
			getenv('PROJECTS_SERVICE_RABBIT_PORT'),
			getenv('PROJECTS_SERVICE_RABBIT_LOGIN'),
			getenv('PROJECTS_SERVICE_RABBIT_PASSWORD')
		);
		$this->channel = $this->connection->channel();
		$this->channel->queue_declare(self::QUEUE_NAME, false, false, false, false);
	}

	public function tearDown()
	{
		$this->mysqli->query('TRUNCATE TABLE projects_users;');
		$this->mysqli->close();

		sleep(1);
		$this->clearQueue();
		$this->channel->close();
		$this->connection->close();
	}

	private function clearQueue()
	{
		do {
			$message = $this->channel->basic_get(self::QUEUE_NAME, true);
		} while ($message !== null);
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
