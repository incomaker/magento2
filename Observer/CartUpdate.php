<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventProduct\EventProductParam;
use Incomaker\Magento2\Async\EventProduct\EventProductPublisher;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CartUpdate implements ObserverInterface {

	private $publisher;

	private $session;

	private LoggerInterface $logger;

	public function __construct(
		EventProductPublisher $publisher,
		Session $session,
		LoggerInterface $logger
	) {
		$this->publisher = $publisher;
		$this->session = $session;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		$this->session->start();
		$quote = $this->session->getQuote();
		$customer = $quote->getCustomer();
		$this->logger->debug("Customer - " . json_encode($customer));

		$new_cart = [];
		foreach ($quote->getAllVisibleItems() as $item) {
			$new_cart[] = $item->getSku();
		}

		$cart_serialized = $this->session->getLastCartState();
		$old_cart = empty($cart_serialized) ? [] : unserialize($cart_serialized);

		$added = array_diff($new_cart, $old_cart);
		$removed = array_diff($old_cart, $new_cart);

		$this->logger->debug("Added - " . json_encode($added));
		$this->logger->debug("Removed - " . json_encode($removed));

		$customerId = $customer ? $customer->getId() : null;

		foreach ($added as $addedSku) {
			$this->publisher->publish(new EventProductParam('cart_add', $customerId, $addedSku, $quote->getId()));
		}

		foreach ($removed as $removedSku) {
			$this->publisher->publish(new EventProductParam('cart_remove', $customerId, $removedSku, $quote->getId()));
		}

		$this->session->setLastCartState(serialize($new_cart));
	}
}
