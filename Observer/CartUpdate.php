<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

class CartUpdate implements ObserverInterface {

	private $incomakerApi;

	private $session;

	private $quote;

	private LoggerInterface $logger;

	public function __construct(
		IncomakerApi $incomakerApi,
		Session $session,
		Quote $quote,
		LoggerInterface $logger
	) {
		$this->incomakerApi = $incomakerApi;
		$this->session = $session;
		$this->quote = $quote;
		$this->logger = $logger;
	}

	public function execute(Observer $observer)	{
		$this->session->start();
		$customer = $this->session->getCustomer();

		$new = array();
		foreach ($this->quote->getAllVisibleItems() as $item) {
			$new[] = $item->getSku();
		}

		$variable = $this->session->getLastCartState();
		$this->logger->debug("Hello - " . $variable);
		$old = empty($variable) ? null : unserialize($variable);

		if (empty($old)) $old = array();
		$diff = array_diff($new, $old);

		if (!empty($diff)) {
			$this->incomakerApi->postProductEvent('cart_add', $customer, current($diff), $this->quote->getId());
		} else {
			$diff = array_diff($old, $new);
			if (!empty($diff)) {
				$this->incomakerApi->postProductEvent('cart_remove', $customer, current($diff), $this->quote->getId());
			}
		}
		$this->session->setLastCartState(serialize($new));
	}
}
