<?php

namespace Incomaker\Magento2\Async\EventUser;

use Incomaker\Magento2\Async\ConsumerBase;

class EventUserConsumer extends ConsumerBase {

	/**
	 * @param EventUserParam $param
	 */
	protected function consume($param) {
		$this->incomakerApi->sendUserEvent($param->eventName, $param->customerId);
	}
}
