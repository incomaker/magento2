<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Async\EventUser\EventUserParam;
use Incomaker\Magento2\Async\EventUser\EventUserPublisher;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ContactLogin implements ObserverInterface {

	private CheckoutSession $checkoutSession;

	private CustomerSession $customerSession;

	private EventUserPublisher $publisher;

	private SerializerInterface $serializer;

	public function __construct(
		EventUserPublisher $publisher,
		CheckoutSession $checkoutSession,
		SerializerInterface $serializer
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->serializer = $serializer;
	}

	public function execute(Observer $observer) {
		$customer = $observer->getData('customer');
		$this->publisher->publish(new EventUserParam('login', $customer->getId()));

		$quote = $this->checkoutSession->getQuote();
		$cart = [];
		foreach ($quote->getAllVisibleItems() as $item) {
			$cart[] = $item->getSku();
		}
		$this->checkoutSession->setLastCartState($this->serializer->serialize($cart));

	}
}
