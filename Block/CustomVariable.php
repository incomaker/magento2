<?php

namespace Incomaker\Magento2\Block;

use Incomaker\Api\Connector;
use Incomaker\Api\DriverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Variable\Model\VariableFactory;

class CustomVariable extends Template {

	private Quote $quote;

	protected $varFactory;

	private DriverInterface $driver;

	public function __construct(
		CheckoutSession $checkoutSession,
		VariableFactory $varFactory,
		Context $context,
		DriverInterface $driver
	) {
		parent::__construct($context);
		$this->varFactory = $varFactory;
		$this->checkoutSession = $checkoutSession;
		$this->driver = $driver;
		$this->quote = $this->checkoutSession->getQuote();
	}

	public function isModuleEnabled() {
		return $this->driver->isModuleEnabled();
	}

	public function getVariableValue($code) {
		$var = $this->varFactory->create();
		$var->loadByCode($code);
		return $var->getValue('text');
	}

	public function getCustomerId() {
		$customer = $this->quote->getCustomer();
		return $customer ? $customer->getId() : null;
	}

	public function getSessionId() {
		return $this->quote->getId();
	}

	public function getApiKey() {
		return $this->driver->getSetting(Connector::INCOMAKER_API_KEY);
	}

	public function getAccountId() {
		return $this->driver->getSetting(Connector::INCOMAKER_ACCOUNT_ID);
	}

	public function getPluginId() {
		return $this->driver->getSetting(Connector::INCOMAKER_PLUGIN_ID);
	}


}
