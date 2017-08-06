<?php

namespace services\AuthService;

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
			getenv('AUTH_SERVICE_MYSQL_HOST'),
			getenv('AUTH_SERVICE_MYSQL_LOGIN'),
			getenv('AUTH_SERVICE_MYSQL_PASSWORD'),
			getenv('AUTH_SERVICE_MYSQL_DATABASE'),
			getenv('AUTH_SERVICE_MYSQL_PORT')
		);

		$this->connection = new AMQPStreamConnection(
			getenv('RABBIT_HOST'),
			getenv('RABBIT_PORT'),
			getenv('RABBIT_LOGIN'),
			getenv('RABBIT_PASSWORD')
		);
		$this->channel = $this->connection->channel();
		$this->channel->queue_declare(self::QUEUE_NAME, false, false, false, false);
	}

	public function tearDown()
	{
		$this->mysqli->query('TRUNCATE TABLE login_password;');
		$this->mysqli->query('TRUNCATE TABLE login_session;');
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
		$url = 'http://auth-service-nginx';

		$client = new Client();
		$response = $client->send(
			new Request($method, $url . $path, [], $bodyContent)
		);

		return (string) $response->getBody();
	}

	/**
	 * @return string[][]
	 */
	protected function getAllStorageEvents()
	{
		sleep(1);

		$messages = [];

		while (true) {
			$message = $this->channel->basic_get(self::QUEUE_NAME, true);

			if ($message === null) {
				break;
			}

			$messages[] = json_decode($message->getBody(), true);
		}

		return $messages;
	}
}
