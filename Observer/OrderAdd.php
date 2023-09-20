<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventOrder\EventOrderParam;
use Incomaker\Magento2\Async\EventOrder\EventOrderPublisher;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

class OrderAdd implements ObserverInterface {

	private EventOrderPublisher $publisher;

	private CustomerSession $customerSession;

	private Quote $quote;

	private LoggerInterface $logger;

	public function __construct(
		EventOrderPublisher $publisher,
		CustomerSession $customerSession,
		Quote $quote,
		LoggerInterface $logger
	) {
		$this->publisher = $publisher;
		$this->customerSession = $customerSession;
		$this->quote = $quote;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		try {
			$this->customerSession->unsLastCartState();
			$order = $observer->getOrder();
			$this->quote->collectTotals();
			$param = new EventOrderParam('order_add', $order->getCustomerId(), $this->quote->getGrandTotal(), $this->quote->getId());
			$this->publisher->publish($param);
		} catch (\Exception $e) {
			$this->logger->error("Incomaker order_add failed: " . $e->getMessage());
		}
	}
}
