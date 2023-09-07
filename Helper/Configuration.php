<?php

namespace Incomaker\Magento2\Helper;

class Configuration
{

	private $scopeConfig;

	private $configWriter;

	const CONFIG_SCOPE = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter
	)
	{
		$this->configWriter = $configWriter;
		$this->scopeConfig = $scopeConfig;
	}

	public function getConfig($config_path, $default = NULL)
	{
		$val = $this->scopeConfig->getValue($config_path, self::CONFIG_SCOPE);
		return $val ?? $default;
	}

	public function setConfig($config_path, $value)
	{
		$this->configWriter->save($config_path, $value, $scope = self::CONFIG_SCOPE);
	}
}
