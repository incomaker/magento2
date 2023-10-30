<?php

namespace Incomaker\Magento2\Async\EventUser;

use Incomaker\Magento2\Async\ParamBase;

class EventUserParam extends ParamBase {

	public $customerId;

	public function __construct(
		$eventName,
		$permId,
		$customerId
	) {
		parent::__construct($eventName, $permId);
		$this->customerId = $customerId;
	}

}
