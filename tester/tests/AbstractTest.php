<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
	/**
	 * @string
	 */
	const EVENTS_QUEUE_NAME = 'events';

	/**
	 * @var AMQPChannel
	 */
	protected $channel;

	/**
	 * @var AMQPStreamConnection
	 */
	protected $connection;

	protected function setUpEventsQueue()
	{
		$this->connection = new AMQPStreamConnection(
			getenv('RABBIT_HOST'),
			getenv('RABBIT_PORT'),
			getenv('RABBIT_LOGIN'),
			getenv('RABBIT_PASSWORD')
		);
		$this->channel = $this->connection->channel();
		$this->channel->queue_declare(self::EVENTS_QUEUE_NAME, false, false, false, false);
	}

	protected function tearDownEventsQueue()
	{
		sleep(1);
		$this->clearQueue();
		$this->channel->close();
		$this->connection->close();
	}

	/**
	 * @return string[][]
	 */
	protected function getAllStorageEvents()
	{
		sleep(1);

		$messages = [];

		while (true) {
			$message = $this->channel->basic_get(self::EVENTS_QUEUE_NAME, true);

			if ($message === null) {
				break;
			}

			$messages[] = json_decode($message->getBody(), true);
		}

		return $messages;
	}

	/**
	 * @return void
	 */
	protected function clearQueue()
	{
		do {
			$message = $this->channel->basic_get(self::EVENTS_QUEUE_NAME, true);
		} while ($message !== null);
	}
}
