<?php

namespace Incomaker\Magento2\Async;

abstract class ParamBase {

	public $eventName;

	public \DateTime $time;

	public function __construct($eventName = 'default') {
		$this->time = new \DateTime();
		$this->eventName = $eventName;
	}

}
