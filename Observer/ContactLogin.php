<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class ContactLogin implements ObserverInterface {

	private CheckoutSession $checkoutSession;

	private EventUserPublisher $publisher;

	private LoggerInterface $logger;

	private DriverInterface $driver;

	public function __construct(
		EventUserPublisher $publisher,
		CheckoutSession $checkoutSession,
		LoggerInterface $logger,
		DriverInterface $driver
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->logger = $logger;
		$this->driver = $driver;
	}

	public function execute(Observer $observer) {
		if (!$this->driver->isModuleEnabled()) return;

		try {
			$customer = $observer->getData('customer');
			$this->publisher->publish(new EventUserParam('login', $customer->getId()));

			$quote = $this->checkoutSession->getQuote();
			$cart = [];
			foreach ($quote->getAllVisibleItems() as $item) {
				$cart[] = $item->getSku();
			}
			$this->checkoutSession->setLastCartState(serialize($cart));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker login event failed: " . $e->getMessage());
		}
	}
}
