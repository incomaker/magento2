<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventOrder\EventOrderParam;
use Incomaker\Magento2\Async\EventOrder\EventOrderPublisher;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderAdd implements ObserverInterface {

	private EventOrderPublisher $publisher;

	private CheckoutSession $checkoutSession;

	private LoggerInterface $logger;

	public function __construct(
		EventOrderPublisher $publisher,
		CheckoutSession $checkoutSession,
		LoggerInterface $logger
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		try {
			$this->checkoutSession->unsLastCartState();
			$quote = $this->checkoutSession->getQuote();
			$quote->collectTotals();
			$order = $observer->getOrder();
			$param = new EventOrderParam('order_add', $order->getCustomerId(), $quote->getGrandTotal(), $quote->getId());
			$this->publisher->publish($param);
		} catch (\Exception $e) {
			$this->logger->error("Incomaker order_add failed: " . $e->getMessage());
		}
	}
}
