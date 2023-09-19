<?php

namespace Incomaker\Magento2\Async\EventUser;

class EventUserParam {

	public string $eventName;

	public $customerId;

	public function __construct(
		string $eventName,
		$customerId
	) {
		$this->eventName = $eventName;
		$this->customerId = $customerId;
	}

}
