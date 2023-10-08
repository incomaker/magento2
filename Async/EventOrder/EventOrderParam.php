<?php

namespace Incomaker\Magento2\Async\EventOrder;

use Incomaker\Magento2\Async\ParamBase;

class EventOrderParam extends ParamBase {

	public $customerId;

	public $total;

	public $sessionId;

	public function __construct(
		$eventName,
		$customerId,
		$total,
		$sessionId
	) {
		parent::__construct($eventName);
		$this->customerId = $customerId;
		$this->total = $total;
		$this->sessionId = $sessionId;
	}

}
