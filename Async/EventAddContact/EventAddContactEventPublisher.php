<?php

namespace Incomaker\Magento2\Async\EventAddContact;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;

class EventAddContactEventPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		LoggerInterface $logger
	) {
		parent::__construct(
			'incomaker.event.add-contact',
			$publisher,
			$logger
		);
	}

}
