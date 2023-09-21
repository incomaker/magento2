<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventUser\EventUserEventPublisher;
use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ContactLogout implements ObserverInterface {

	private $publisher;

	public function __construct(
		EventUserEventPublisher $publisher
	) {
		$this->publisher = $publisher;
	}

	public function execute(Observer $observer) {
		$customer = $observer->getData('customer');
		$this->publisher->publish(new EventUserParam('logout', $customer->getId()));
	}
}
