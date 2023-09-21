<?php

namespace Incomaker\Magento2\Async;

abstract class ParamBase {

	public string $eventName;

	public \DateTime $time;

	public function __construct(string $eventName = 'default') {
		$this->time = new \DateTime();
		$this->eventName = $eventName;
	}

}
