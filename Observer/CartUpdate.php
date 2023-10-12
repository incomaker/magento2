<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventProduct\EventProductParam;
use Incomaker\Magento2\Async\EventProduct\EventProductPublisher;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CartUpdate implements ObserverInterface {

	private EventProductPublisher $publisher;

	private CheckoutSession $checkoutSession;

	private SerializerInterface $serializer;

	public function __construct(
		EventProductPublisher $publisher,
		CheckoutSession $checkoutSession,
		SerializerInterface $serializer
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->serializer = $serializer;
	}

	public function execute(Observer $observer) {
		try {
			$quote = $this->checkoutSession->getQuote();
			$customer = $quote->getCustomer();
			$customerId = $customer ? $customer->getId() : null;

		$new_cart = [];
		foreach ($quote->getAllVisibleItems() as $item) {
			$new_cart[] = $item->getSku();
		}

		$cart_serialized = $this->checkoutSession->getLastCartState();
		$old_cart = empty($cart_serialized) ? [] : unserialize($cart_serialized);

		$added = array_diff($new_cart, $old_cart);
		$removed = array_diff($old_cart, $new_cart);

		foreach ($added as $addedSku) {
			$this->publisher->publish(new EventProductParam('cart_add', $customerId, $addedSku, $quote->getId()));
		}

		foreach ($removed as $removedSku) {
			$this->publisher->publish(new EventProductParam('cart_remove', $customerId, $removedSku, $quote->getId()));
		}

			$this->checkoutSession->setLastCartState(serialize($new_cart));
		} catch (\Exception $e) {
			$this->logger->error("Incomaker cart update event failed: " . $e->getMessage());
		}
	}
}
