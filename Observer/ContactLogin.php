<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class ContactLogin implements ObserverInterface {

	private CheckoutSession $checkoutSession;

	private EventUserPublisher $publisher;

	private SerializerInterface $serializer;

	private LoggerInterface $logger;

	private DriverInterface $driver;

	private IncomakerApi $api;

	public function __construct(
		EventUserPublisher $publisher,
		SerializerInterface $serializer,
		CheckoutSession $checkoutSession,
		LoggerInterface $logger,
		DriverInterface $driver,
		IncomakerApi $api
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->serializer = $serializer;
		$this->logger = $logger;
		$this->driver = $driver;
		$this->api = $api;
	}

	public function execute(Observer $observer) {
		if (!$this->driver->isModuleEnabled()) return;

		try {
			$customer = $observer->getData('customer');
			$this->publisher->publish(new EventUserParam('login', $this->api->getPermId(), $customer->getId()));

			$quote = $this->checkoutSession->getQuote();
			$cart = [];
			foreach ($quote->getAllVisibleItems() as $item) {
				$cart[] = $item->getSku();
			}
			$this->checkoutSession->setLastCartState($this->serializer->serialize($cart));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker login event failed: " . $e->getMessage());
		}
	}
}
