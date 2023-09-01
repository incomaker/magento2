<?php

namespace Incomaker\Magento2\Helper;

class Configuration
{

	private $scopeConfig;
	private $configWriter;

	const CONFIG_SCOPE = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

	public function __construct(
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Magento\Store\Model\StoreManagerInterface            $storeManager
	)
	{
		$this->_logger = $logger;
		$this->_configWriter = $configWriter;
		$this->_storeManager = $storeManager;
	}

	public function getConfig($config_path, $default = NULL)
	{
		$val = $this->scopeConfig->getValue($config_path, self::CONFIG_SCOPE);
		return $val ?? $default;
	}

	public function setConfig($config_path, $value)
	{
		$this->scopeConfig->setValue(
			$config_path, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
		);
	}
}
