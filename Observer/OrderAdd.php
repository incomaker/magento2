<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventOrder\EventOrderParam;
use Incomaker\Magento2\Async\EventOrder\EventOrderPublisher;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart;
use Psr\Log\LoggerInterface;

class OrderAdd implements ObserverInterface {

	private EventOrderPublisher $publisher;

	private CustomerSession $customerSession;

	private Cart $cart;

	private LoggerInterface $logger;

	public function __construct(
		EventOrderPublisher $publisher,
		CustomerSession $customerSession,
		Cart $cart,
		LoggerInterface $logger
	) {
		$this->publisher = $publisher;
		$this->customerSession = $customerSession;
		$this->cart = $cart;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		try {
			$this->customerSession->unsLastCartState();
			$quote = $this->cart->getQuote();
			$quote->collectTotals();
			$order = $observer->getOrder();
			$param = new EventOrderParam('order_add', $order->getCustomerId(), $quote->getGrandTotal(), $quote->getId());
			$this->publisher->publish($param);
		} catch (\Exception $e) {
			$this->logger->error("Incomaker order_add failed: " . $e->getMessage());
		}
	}
}
