<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class ContactLogout implements ObserverInterface {

	private $publisher;

	private LoggerInterface $logger;

	public function __construct(
		EventUserPublisher $publisher,
		LoggerInterface $logger
	) {
		$this->publisher = $publisher;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		try {
			$customer = $observer->getData('customer');
			$this->publisher->publish(new EventUserParam('logout', $customer->getId()));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker logout event failed: " . $e->getMessage());
		}
	}
}
