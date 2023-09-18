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

	public function publish($param) {
		$message = $this->serialize($param);
		$this->logger->debug("Publishing Message " . $message . " into topic " . $this->topicName);
		$this->publisher->publish($this->topicName, $message);
	}
}
