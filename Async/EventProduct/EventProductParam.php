<?php

namespace Incomaker\Magento2\Async\EventProduct;

use Incomaker\Magento2\Async\ParamBase;

class EventProductParam extends ParamBase {

	public $customerId;

	public $productId;

	public $sessionId;

	public function __construct(
		$eventName,
		$customerId,
		$productId,
		$sessionId
	) {
		parent::__construct($eventName);
		$this->customerId = $customerId;
		$this->productId = $productId;
		$this->sessionId = $sessionId;
	}

}
