<?php

namespace Incomaker\Magento2\Helper;

use Incomaker\Api\Connector;
use Incomaker\Api\DriverInterface;

class IncomakerDriver implements DriverInterface {

	const CONFIG_KEY_MAP = [
		Connector::INCOMAKER_ENABLED => 'incomaker/settings/enabled',
		Connector::INCOMAKER_API_KEY => 'incomaker/settings/api_key',
		Connector::INCOMAKER_ACCOUNT_ID => 'incomaker/settings/account_id',
		Connector::INCOMAKER_PLUGIN_ID => 'incomaker/settings/plugin_id'
	];

	/**
	 * @var Configuration
	 */
	private $configuration;

	public function __construct(Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getSetting($key) {
		return $this->configuration->getConfig(self::CONFIG_KEY_MAP[$key]);
	}

	public function updateSetting($key, $value) {
		$this->configuration->setConfig(self::CONFIG_KEY_MAP[$key], $value);
	}

	public function isModuleEnabled() {
		return $this->getSetting(Connector::INCOMAKER_ENABLED);
	}
}
