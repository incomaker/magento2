<?php

namespace Incomaker\Magento2\Async\EventOrder;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;

class EventOrderEventPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		LoggerInterface $logger
	) {
		parent::__construct(
			'incomaker.event.order',
			$publisher,
			$logger
		);
	}

}
