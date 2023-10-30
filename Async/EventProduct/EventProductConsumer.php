<?php

namespace Incomaker\Magento2\Async\EventProduct;

use Incomaker\Magento2\Async\ConsumerBase;

class EventProductConsumer extends ConsumerBase {

	/**
	 * @param EventProductParam $param
	 */
	protected function consume($param) {
		$this->incomakerApi->sendProductEvent($param->eventName, $param->permId, $param->customerId, $param->productId, $param->sessionId, $param->time);
	}
}
