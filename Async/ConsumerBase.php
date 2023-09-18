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
		$this->logger->debug("ConsumerBase->process");
		$this->logger->debug("Param - " . $param);
		$paramDeser = $this->deserialize($param);
		$this->logger->debug("Parameter - " . print_r($paramDeser, true));
		$this->consume($paramDeser);
	}

	protected abstract function consume($param);
}
