<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CartUpdate implements ObserverInterface {

	private $incomakerApi;

	private $session;

	private LoggerInterface $logger;

	public function __construct(
		IncomakerApi $incomakerApi,
		Session $session,
		LoggerInterface $logger
	) {
		$this->incomakerApi = $incomakerApi;
		$this->session = $session;
		$this->logger = $logger;
	}

	public function execute(Observer $observer)	{
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

		foreach ($added as $addedSku) {
			$this->incomakerApi->postProductEvent('cart_add', $customer, $addedSku, $quote->getId());
		}

		foreach ($removed as $removedSku) {
			$this->incomakerApi->postProductEvent('cart_remove', $customer, $removedSku, $quote->getId());
		}

		$this->session->setLastCartState(serialize($new_cart));
	}
}
