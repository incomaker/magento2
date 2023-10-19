<?php

namespace Incomaker\Magento2\Block\Adminhtml\System\Config;

use Incomaker\Magento2\Helper\IncomakerApi;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SaveActionObserver implements ObserverInterface {

	private IncomakerApi $incomakerApi;

	private LoggerInterface $logger;

	public function __construct(
		IncomakerApi $incomakerApi,
		LoggerInterface $logger
	) {
		$this->incomakerApi = $incomakerApi;
		$this->logger = $logger;
	}

	public function execute(Observer $observer) {
		$this->incomakerApi->checkPluginInfo();
	}

}
