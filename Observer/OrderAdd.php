<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

class OrderAdd implements ObserverInterface {

	private $incomakerApi;

	private CustomerSession $customerSession;

	private $quote;

	public function __construct(
		IncomakerApi $incomakerApi,
		CustomerSession $customerSession,
		Quote $quote
	) {
		$this->incomakerApi = $incomakerApi;
		$this->customerSession = $customerSession;
		$this->quote = $quote;
	}

	public function execute(Observer $observer) {
		$order = $observer->getOrder();
		$this->quote->collectTotals();

		$this->incomakerApi->sendOrderEvent('order_add', $order->getCustomerId(), $this->quote->getGrandTotal(), $this->quote->getId());
		//TODO Fix wrong posting of Total instead of orderId

		//$this->customerSession->start();
		$this->customerSession->unsLastCartState();
	}
}
