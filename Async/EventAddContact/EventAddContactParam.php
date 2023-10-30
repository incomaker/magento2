<?php

namespace Incomaker\Magento2\Async\EventAddContact;

use Incomaker\Magento2\Async\ParamBase;

class EventAddContactParam extends ParamBase {

	public $customerId;

	public function __construct(
		$customerId,
		$permId
	) {
		parent::__construct('default', $permId);
		$this->customerId = $customerId;
	}

}
