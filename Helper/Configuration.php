<?php

namespace Incomaker\Magento2\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper {

    public function getConfig($config_path, $default = NULL)
    {
        $val = $this->scopeConfig->getValue(
            $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if (isset($val)) {
            return $val;
        } else {
            return $default;
        }
    }
}