<?php

namespace Incomaker\Magento2\Async\EventUser;

use Incomaker\Magento2\Async\PublisherBase;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class EventUserPublisher extends PublisherBase {

	public function __construct(
		PublisherInterface $publisher,
		LoggerInterface $logger,
		SerializerInterface $serializer
	) {
		parent::__construct(
			'incomaker.event.user',
			$publisher,
			$logger,
			$serializer
		);
	}

}
