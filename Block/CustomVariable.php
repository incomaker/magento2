<?php

namespace Incomaker\Magento2\Block;

class CustomVariable extends \Magento\Framework\View\Element\Template
{
    protected $_varFactory;

    public function __construct(
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Framework\View\Element\Template\Context $context)
    {
        $this->_varFactory = $varFactory;
        parent::__construct($context);
    }

    public function getVariableValue($code) {
        $var = $this->_varFactory->create();
        $var->loadByCode($code);
        return $var->getValue('text');
    }
}
