<?php

namespace Incomaker\Magento2\Async\EventProduct;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;

class EventProductPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		LoggerInterface $logger
	) {
		parent::__construct(
			'incomaker.event.product',
			$publisher,
			$logger
		);
	}

}
