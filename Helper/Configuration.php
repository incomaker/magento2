<?php

namespace Incomaker\Magento2\Helper;

use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Configuration extends AbstractHelper {

	private WriterInterface $configWriter;

	const CONFIG_SCOPE = ScopeInterface::SCOPE_WEBSITES;

	private StoreManagerInterface $storeManager;

	private Config $configCache;

	public function __construct(
		Context $context,
		WriterInterface $configWriter,
		StoreManagerInterface $storeManager,
		Config $configCache
	) {
		parent::__construct($context);
		$this->configWriter = $configWriter;
		$this->storeManager = $storeManager;
		$this->configCache = $configCache;
	}

	public function getWebsiteId(): int {
		try {
			return $this->storeManager->getStore()->getWebsiteId();
		} catch (\Exception $e) {
			return 1;
		}
	}

	public function getConfig($config_path, $default = NULL) {
		return $this->scopeConfig->getValue($config_path, self::CONFIG_SCOPE) ?? $default;
	}

	public function setConfig($config_path, $value): void {
		$this->configWriter->save($config_path, $value, self::CONFIG_SCOPE, $this->getWebsiteId());
		$this->configCache->clean();
	}
}
