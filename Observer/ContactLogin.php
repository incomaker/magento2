<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ContactLogin implements ObserverInterface {

	private $publisher;

	public function __construct(
		EventUserPublisher $publisher
	) {
		$this->publisher = $publisher;
	}

	public function execute(Observer $observer) {
		$customer = $observer->getData('customer');
		$this->publisher->publish(new EventUserParam('login', $customer->getId()));
	}
}
