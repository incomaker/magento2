<?php

namespace Incomaker\Magento2\Async\EventProduct;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class EventProductPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		Json $json,
		LoggerInterface $logger
	) {
		parent::__construct(
			'incomaker.event.product',
			$publisher,
			$json,
			$logger
		);
	}
	
}
