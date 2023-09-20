<?php

namespace Incomaker\Magento2\Async\EventAddContact;

class EventAddContactParam {

	public $customerId;

	public function __construct(
		$customerId
	) {
		$this->customerId = $customerId;
	}

}
