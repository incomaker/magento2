<?php

	namespace Incomaker\Magento2\Observer;

	use Incomaker\Magento2\Helper\IncomakerApi;
	use Magento\Framework\Event\Observer;
	use Magento\Framework\Event\ObserverInterface;

	class ContactLogin implements ObserverInterface {

		private $incomakerApi;

		public function __construct(
			IncomakerApi $incomakerApi,
		) {
			$this->incomakerApi = $incomakerApi;
		}

		public function execute(Observer $observer) {
			$customer = $observer->getData('customer');
			$this->incomakerApi->postEvent("login", $customer);
		}
	}
