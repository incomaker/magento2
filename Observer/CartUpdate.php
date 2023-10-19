<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Async\EventProduct\EventProductParam;
use Incomaker\Magento2\Async\EventProduct\EventProductPublisher;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class CartUpdate implements ObserverInterface {

	private EventProductPublisher $publisher;

	private CheckoutSession $checkoutSession;

	private SerializerInterface $serializer;

	private LoggerInterface $logger;

	private DriverInterface $driver;

	public function __construct(
		EventProductPublisher $publisher,
		CheckoutSession $checkoutSession,
		SerializerInterface $serializer,
		LoggerInterface $logger,
		DriverInterface $driver
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->logger = $logger;
		$this->serializer = $serializer;
		$this->driver = $driver;
	}

	public function execute(Observer $observer) {
		if (!$this->driver->isModuleEnabled()) return;

		try {
			$quote = $this->checkoutSession->getQuote();
			$customer = $quote->getCustomer();
			$customerId = $customer ? $customer->getId() : null;

			$new_cart = [];
			foreach ($quote->getAllVisibleItems() as $item) {
				$new_cart[] = $item->getSku();
			}

			$cart_serialized = $this->checkoutSession->getLastCartState();
			$old_cart = empty($cart_serialized) ? [] : $this->serializer->unserialize($cart_serialized);

			$added = array_diff($new_cart, $old_cart);
			$removed = array_diff($old_cart, $new_cart);

			foreach ($added as $addedSku) {
				$this->publisher->publish(new EventProductParam('cart_add', $customerId, $addedSku, $quote->getId()));
			}

			foreach ($removed as $removedSku) {
				$this->publisher->publish(new EventProductParam('cart_remove', $customerId, $removedSku, $quote->getId()));
			}

			$this->checkoutSession->setLastCartState($this->serializer->serialize($new_cart));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker cart update event failed: " . $e->getMessage());
		}
	}
}
