<?php

namespace Incomaker\Magento2\Async\EventAddContact;

use Incomaker\Magento2\Async\ParamBase;

class EventAddContactParam extends ParamBase {

	public $customerId;

	public function __construct(
		$customerId
	) {
		parent::__construct();
		$this->customerId = $customerId;
	}

}
