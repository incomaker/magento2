<?php

namespace Incomaker\Magento2\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Configuration extends AbstractHelper {

	private WriterInterface $configWriter;

	const CONFIG_SCOPE = ScopeInterface::SCOPE_WEBSITE;

	public function __construct(
		Context $context,
		WriterInterface $configWriter
	) {
		parent::__construct($context);
		$this->configWriter = $configWriter;
	}

	public function getConfig($config_path, $default = NULL): mixed	{
		return $this->scopeConfig->getValue($config_path, self::CONFIG_SCOPE) ?? $default;
	}

	public function setConfig($config_path, $value): void {
		$this->configWriter->save($config_path, $value, $scope = self::CONFIG_SCOPE);
	}
}
