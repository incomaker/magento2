<?php

namespace Incomaker\Magento2\Async\EventOrder;

use Incomaker\Magento2\Async\ConsumerBase;

class EventOrderConsumer extends ConsumerBase {

	/**
	 * @param EventOrderParam $param
	 */
	protected function consume($param) {
		$this->incomakerApi->sendOrderEvent($param->eventName, $param->customerId, $param->total, $param->sessionId);
	}
}
