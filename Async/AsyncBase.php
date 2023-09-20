<?php

namespace Incomaker\Magento2\Async;

use Psr\Log\LoggerInterface;

abstract class AsyncBase {

	protected LoggerInterface $logger;

	public function __construct(
		LoggerInterface $logger
	) {
		$this->logger = $logger;
	}

	protected function serialize($param) {
		return serialize($param);
	}

	protected function deserialize(string $str) {
		return unserialize($str);
	}
}
