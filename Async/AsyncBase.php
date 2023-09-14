<?php

namespace Incomaker\Magento2\Async;

use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

abstract class AsyncBase {

	private Json $json;

	protected LoggerInterface $logger;

	public function __construct(
		Json $json,
		LoggerInterface $logger
	) {
		$this->json = $json;
		$this->logger = $logger;
	}

	protected function serialize(object $param) {
		return $this->json->serialize($param);
	}

	protected function deserialize(string $str) {
		return $this->json->unserialize($str);
	}
}
