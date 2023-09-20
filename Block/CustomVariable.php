<?php

namespace Incomaker\Magento2\Block;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Variable\Model\VariableFactory;

class CustomVariable extends Template {

	private Quote $quote;

	protected $varFactory;

	public function __construct(
		CheckoutSession $checkoutSession,
		VariableFactory $varFactory,
		Context $context
	) {
		parent::__construct($context);
		$this->varFactory = $varFactory;
		$this->checkoutSession = $checkoutSession;
		$this->quote = $this->checkoutSession->getQuote();

	}

	public function getVariableValue($code) {
		$var = $this->varFactory->create();
		$var->loadByCode($code);
		return $var->getValue('text');
	}

	public function getCustomerId() {
		return $this->quote->getCustomerId();
	}

	public function getSessionId() {
		return $this->quote->getId();
	}

}
