<?php

namespace Incomaker\Magento2\Async\EventOrder;

use Incomaker\Magento2\Async\ParamBase;

class EventOrderParam extends ParamBase {

	public $customerId;

	public $total;

	public string $sessionId;

	public function __construct(
		string $eventName,
		$customerId,
		$total,
		string $sessionId
	) {
		parent::__construct($eventName);
		$this->customerId = $customerId;
		$this->total = $total;
		$this->sessionId = $sessionId;
	}

}
