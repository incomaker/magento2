<?php

namespace Incomaker\Magento2\Async;

use Incomaker\Magento2\Helper\IncomakerApi;
use Psr\Log\LoggerInterface;

abstract class ConsumerBase extends AsyncBase {

	protected IncomakerApi $incomakerApi;

	public function __construct(
		LoggerInterface $logger,
		IncomakerApi $incomakerApi
	) {
		parent::__construct($logger);
		$this->incomakerApi = $incomakerApi;
	}

	public function process(string $param) {
		$this->logger->info("Consuming message - " . $param);
		$this->consume($this->deserialize($param));
	}

	protected abstract function consume($param);
}
