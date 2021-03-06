<?php

namespace Incomaker\Magento2\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper {

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}