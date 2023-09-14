<?php

namespace Incomaker\Magento2\Async;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

abstract class PublisherBase extends AsyncBase {

	private string $topicName;

	private PublisherInterface $publisher;

	public function __construct(
		string $topicName,
		PublisherInterface $publisher,
		Json $json,
		LoggerInterface $logger
	) {
		parent::__construct($json, $logger);
		$this->topicName = $topicName;
		$this->publisher = $publisher;
	}

	public function publish(object $param) {
		$message = $this->serialize($param);
		$this->logger->debug("Publishing message to topic " . $this->topicName . ", message: " . $message);
		$this->publisher->publish($this->topicName, $message);
	}
}
