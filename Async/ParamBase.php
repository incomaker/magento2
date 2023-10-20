<?php

namespace Incomaker\Magento2\Async;

abstract class ParamBase {

	public $eventName;

	public $time;

	public function __construct($eventName = 'default') {
		$this->time = (new \DateTime())->format("c");
		$this->eventName = $eventName;
	}

}
