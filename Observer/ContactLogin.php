<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
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

	public function __construct(
		EventUserPublisher $publisher,
		SerializerInterface $serializer,
		CheckoutSession $checkoutSession,
		LoggerInterface $logger
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->serializer = $serializer;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		try {
			$customer = $observer->getData('customer');
			$this->publisher->publish(new EventUserParam('login', $customer->getId()));

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
