<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Async\EventAddContact\EventAddContactParam;
use Incomaker\Magento2\Async\EventAddContact\EventAddContactPublisher;
use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class ContactRegistration implements ObserverInterface {

	private $addContactPublisher;

	private $userPublisher;

	private LoggerInterface $logger;

	private DriverInterface $driver;

	public function __construct(
		EventAddContactPublisher $addContactPublisher,
		EventUserPublisher $userPublisher,
		LoggerInterface $logger,
		DriverInterface $driver
	) {
		$this->addContactPublisher = $addContactPublisher;
		$this->userPublisher = $userPublisher;
		$this->logger = $logger;
		$this->driver = $driver;
	}

	public function execute(Observer $observer) {
		if (!$this->driver->isModuleEnabled()) return;

		try {
			$customer = $observer->getData('customer');
			$this->addContactPublisher->publish(new EventAddContactParam($customer->getId()));
			$this->userPublisher->publish(new EventUserParam('register', $customer->getId()));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker register event failed: " . $e->getMessage());
		}
	}

}
