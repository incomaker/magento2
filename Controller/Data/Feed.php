<?php

namespace Incomaker\Magento2\Controller\Data;

use Incomaker\Api\DriverInterface;
use Incomaker\Magento2\Helper\ExportManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\ScopeInterface;

class Feed implements ActionInterface {

	protected $context;
	protected $scopeConfig;
	protected $pageFactory;
	protected $fileFactory;
	protected $manager;
	private DriverInterface $driver;

	/**
	 * @param Context $context
	 * @param RawFactory $pageFactory
	 * @param ScopeConfigInterface $scopeConfig
	 * @param ExportManager $manager
	 */
	public function __construct(
		Context $context,
		RawFactory $pageFactory,
		ScopeConfigInterface $scopeConfig,
		ExportManager $manager,
		DriverInterface $driver
	) {
		$this->context = $context;
		$this->pageFactory = $pageFactory;
		$this->scopeConfig = $scopeConfig;
		$this->manager = $manager;
		$this->driver = $driver;
	}

	private function createResult($code, $content, $contentType = 'text/plain') {
		$result = $this->pageFactory->create();
		$result->setHttpResponseCode($code);
		$result->setHeader('Content-Type', $contentType);
		$result->setContents($content);
		return $result;
	}

	public function execute() {
		if (!$this->driver->isModuleEnabled()) {
			return $this->createResult(503, "Incomaker module is disabled!");
		}

		$params = $this->context->getRequest()->getParams();
		$exportType = isset($params["type"]) ? $params["type"] : null;

		if (!$this->manager->exportExists($exportType)) {
			return $this->createResult(WebApiException::HTTP_BAD_REQUEST, "Invalid feed type!");
		}

		if (!isset($params["key"]) || $this->scopeConfig->getValue('incomaker/settings/api_key', ScopeInterface::SCOPE_WEBSITE) != $params["key"]) {
			return $this->createResult(WebApiException::HTTP_BAD_REQUEST, "Invalid API key!");
		}

		$xmlExport = $this->manager->getExport($exportType);

		try {
			$xmlExport->setGenerate(isset($params["downloadCount"]) ? $params["downloadCount"] : NULL);
			$xmlExport->setLimit(isset($params["limit"]) ? $params["limit"] : NULL);
			$xmlExport->setOffset(isset($params["offset"]) ? $params["offset"] : NULL); //TODO Offsets higher than the number of items returns bad results
			$xmlExport->setId(isset($params["id"]) ? $params["id"] : NULL);
			$xmlExport->setSince(isset($params["since"]) ? $params["since"] : NULL);    //TODO Since date format check
			return $this->createResult(200, $xmlExport->createXmlFeed(), 'text/xml');
		} catch (\InvalidArgumentException $e) {
			return $this->createResult(WebApiException::HTTP_INTERNAL_ERROR, $e->getMessage());
		}
	}
}
