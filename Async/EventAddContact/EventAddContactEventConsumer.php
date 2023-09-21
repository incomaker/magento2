<?php

namespace Incomaker\Magento2\Async\EventAddContact;

use Incomaker\Magento2\Async\ConsumerBase;

class EventAddContactEventConsumer extends ConsumerBase {

	/**
	 * @param EventAddContactParam $param
	 */
	protected function consume($param) {
		$this->incomakerApi->sendAddContactEvent($param->customerId);
	}
}
