<?php

namespace Incomaker\Magento2\Async\EventProduct;

use Incomaker\Magento2\Async\ConsumerBase;

class EventProductConsumer extends ConsumerBase {

	protected function consume($param) {
		$this->logger->debug("Consuming EventProduct message: " . $this->serialize($param));

		/**
		 * @type EventProductParam $productParam
		 */
		$productParam = $param;
		$this->incomakerApi->postProductEvent($productParam->eventName, $productParam->customerId, $productParam->productId, $productParam->sessionId);
	}
}
