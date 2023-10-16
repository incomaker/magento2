<?php

namespace Incomaker\Magento2\Async;

use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

abstract class AsyncBase {

	protected LoggerInterface $logger;

	protected SerializerInterface $serializer;

	public function __construct(
		LoggerInterface $logger,
		SerializerInterface $serializer
	) {
		$this->logger = $logger;
		$this->serializer = $serializer;
	}

	protected function serialize($param) {
		return $this->serializer->serialize($param);
	}

	protected function deserialize(string $str): ?object {
		try {
			return (object)$this->serializer->unserialize($str);
		} catch (\Exception $e) {
			$this->logger->error("Error when deserializing queue message: $str");
			return null;
		}
	}

}
