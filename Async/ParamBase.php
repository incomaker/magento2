<?php

namespace Incomaker\Magento2\Async;

abstract class ParamBase {

	public $eventName;

	public $permId;

	public $time;

	public function __construct($eventName = 'default', $permId = null) {
		$this->time = (new \DateTime())->format("c");
		$this->eventName = $eventName;
		$this->permId = $permId;
	}

}
