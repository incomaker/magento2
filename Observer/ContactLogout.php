<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class ContactLogout implements ObserverInterface {

	private $publisher;

	private LoggerInterface $logger;

	private DriverInterface $driver;

	private IncomakerApi $api;

	public function __construct(
		EventUserPublisher $publisher,
		LoggerInterface $logger,
		DriverInterface $driver,
		IncomakerApi $api
	) {
		$this->publisher = $publisher;
		$this->logger = $logger;
		$this->driver = $driver;
		$this->api = $api;
	}

	public function execute(Observer $observer) {
		if (!$this->driver->isModuleEnabled()) return;

		try {
			$customer = $observer->getData('customer');
			$this->publisher->publish(new EventUserParam('logout', $this->api->getPermId(), $customer->getId()));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker logout event failed: " . $e->getMessage());
		}
	}
}
