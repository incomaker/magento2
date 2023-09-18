<?php

namespace Incomaker\Magento2\Async\EventProduct;

class EventProductParam {

	public string $eventName;

	public $customerId;

	public string $productId;

	public string $sessionId;

	public function __construct(
		string $eventName,
		$customerId,
		string $productId,
		string $sessionId
	) {
		$this->eventName = $eventName;
		$this->customerId = $customerId;
		$this->productId = $productId;
		$this->sessionId = $sessionId;
	}

}
