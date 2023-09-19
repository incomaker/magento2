<?php

namespace Incomaker\Magento2\Async\EventOrder;

class EventOrderParam {

	public string $eventName;

	public $customerId;

	public $total;

	public string $sessionId;

	public function __construct(
		string $eventName,
		$customerId,
		$total,
		string $sessionId
	) {
		$this->eventName = $eventName;
		$this->customerId = $customerId;
		$this->total = $total;
		$this->sessionId = $sessionId;
	}

}
