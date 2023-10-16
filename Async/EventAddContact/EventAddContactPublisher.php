<?php

namespace Incomaker\Magento2\Async\EventAddContact;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class EventAddContactPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		LoggerInterface $logger,
		SerializerInterface $serializer
	) {
		parent::__construct(
			'incomaker.event.add-contact',
			$publisher,
			$logger,
			$serializer
		);
	}

}
