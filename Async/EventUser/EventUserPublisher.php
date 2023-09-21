<?php

namespace Incomaker\Magento2\Async\EventUser;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;

class EventUserPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		LoggerInterface $logger
	) {
		parent::__construct(
			'incomaker.event.user',
			$publisher,
			$logger
		);
	}

}
