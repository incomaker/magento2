<?php

	namespace Incomaker\Magento2\Observer;

	use Incomaker\Magento2\Helper\IncomakerApi;
	use Magento\Framework\Event\Observer;
	use Magento\Framework\Event\ObserverInterface;
	use Magento\Checkout\Model\Session;
	use Magento\Quote\Model\Quote;

	class OrderAdd implements ObserverInterface {

		private $incomakerApi;

		private $session;

		private $quote;

		public function __construct(
			IncomakerApi $incomakerApi,
			Session $session,
			Quote $quote
		) {
			$this->incomakerApi = $incomakerApi;
			$this->session = $session;
			$this->quote = $quote;
		}

		public function execute(Observer $observer) {
			$order = $observer->getOrder();
			$this->quote->collectTotals();

			$this->incomakerApi->postOrderEvent('order_add', $order->getCustomerId(), $this->quote->getGrandTotal(), $this->quote->getId());
			//TODO Fix wrong posting of Total instead of orderId

			$this->session->start();
			$this->session->unsLastCartState();
		}
	}
