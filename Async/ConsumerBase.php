<?php

namespace Incomaker\Magento2\Async;

use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

abstract class ConsumerBase extends AsyncBase {

	protected IncomakerApi $incomakerApi;

	public function __construct(
		Json $json,
		LoggerInterface $logger,
		IncomakerApi $incomakerApi
	) {
		parent::__construct($json, $logger);
		$this->incomakerApi = $incomakerApi;
	}

	public function process(string $param) {
		$this->logger->debug("Processing message: " . $param);
		$this->consume($this->deserialize($param));
	}

	protected abstract function consume(object $param);
}
