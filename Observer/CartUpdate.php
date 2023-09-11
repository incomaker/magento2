<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CartUpdate implements ObserverInterface
{
	private $incomakerApi;
	private $customerSession;
	private $cart;

	private LoggerInterface $logger;

	public function __construct(
		IncomakerApi $incomakerApi,
		Session $customerSession,
		Cart $cart,
		LoggerInterface $logger
	) {
		$this->incomakerApi = $incomakerApi;
		$this->customerSession = $customerSession;
		$this->cart = $cart;
		$this->logger = $logger;
	}

	public function execute(Observer $observer)	{
		$this->customerSession->start();
		$customer = $this->customerSession->getCustomer();

		$new = array();
		foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
			$new[] = $item->getSku();
		}

		$variable = $this->customerSession->getLastCartState();
		$this->logger->debug("Hello - " . $variable);
		$old = empty($variable) ? null : unserialize($variable);

		if (empty($old)) $old = array();
		$diff = array_diff($new, $old);

		if (!empty($diff)) {
			$this->incomakerApi->postProductEvent('cart_add', $customer, current($diff), $this->cart->getQuote()->getId());
		} else {
			$diff = array_diff($old, $new);
			if (!empty($diff)) {
				$this->incomakerApi->postProductEvent('cart_remove', $customer, current($diff), $this->cart->getQuote()->getId());
			}
		}
		$this->customerSession->setLastCartState(serialize($new));
	}
}
