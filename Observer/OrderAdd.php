<?php

namespace Incomaker\Magento2\Observer;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Async\EventOrder\EventOrderParam;
use Incomaker\Magento2\Async\EventOrder\EventOrderPublisher;
use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class OrderAdd implements ObserverInterface {

	private EventOrderPublisher $publisher;

	private CheckoutSession $checkoutSession;

	private LoggerInterface $logger;

	private DriverInterface $driver;

	private IncomakerApi $api;

	public function __construct(
		EventOrderPublisher $publisher,
		CheckoutSession $checkoutSession,
		LoggerInterface $logger,
		DriverInterface $driver,
		IncomakerApi $api
	) {
		$this->publisher = $publisher;
		$this->checkoutSession = $checkoutSession;
		$this->logger = $logger;
		$this->driver = $driver;
		$this->api = $api;
	}

	public function execute(Observer $observer) {
		if (!$this->driver->isModuleEnabled()) return;

		try {
			if (isset($this->checkoutSession)) $this->checkoutSession->unsLastCartState();
			/**
			 * @var $order Order
			 */
			$order = $observer->getEvent()->getData('order');
			if (!($order instanceof Order)) {
				$this->logger->error("Incomaker order_add event cannot be processed, because order is empty or of a wrong class!");
				return;
			}
			$param = new EventOrderParam('order_add', $this->api->getPermId(), $order->getCustomerId(), $order->getGrandTotal(), $order->getQuoteId());
			$this->publisher->publish($param);
		} catch (\Exception $e) {
			$this->logger->error("Incomaker order_add failed: " . $e->getMessage());
		}
	}
}
