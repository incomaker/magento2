<?php
namespace Incomaker\Magento2\Helper;

use Incomaker\Api\Connector;
use Incomaker\Api\DriverInterface;

class IncomakerDriver implements DriverInterface
{
    private $value;

    public function __construct(
        Configuration $configuration
    )
    {
        $value[Connector::INCOMAKER_API_KEY] = $configuration->getConfig('incomaker/settings/api_key');
        $value[Connector::INCOMAKER_ACCOUNT_ID] = $configuration->getConfig('incomaker/settings/account_id');
        $value[Connector::INCOMAKER_PLUGIN_ID] = $configuration->getConfig('incomaker/settings/plugin_id');
    }

    public function getSetting($key) {
        return $this->value[$key];
    }

    public function updateSetting($key, $value) {
        $this->value[$key] = $value;
    }

}