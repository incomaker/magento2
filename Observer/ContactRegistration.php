<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventAddContact\EventAddContactParam;
use Incomaker\Magento2\Async\EventAddContact\EventAddContactPublisher;
use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ContactRegistration implements ObserverInterface {

	private $addContactPublisher;

	private $userPublisher;

	public function __construct(
		EventAddContactPublisher $addContactPublisher,
		EventUserPublisher $userPublisher
	) {
		$this->addContactPublisher = $addContactPublisher;
		$this->userPublisher = $userPublisher;
	}

	public function execute(Observer $observer) {
		$customer = $observer->getData('customer');
		$this->addContactPublisher->publish(new EventAddContactParam($customer->getId()));
		$this->userPublisher->publish(new EventUserParam('register', $customer->getId()));
	}
}
