<?php

namespace Incomaker\Magento2\Helper;

class ExportManager
{

    protected $exports;

    public function __construct(
        \Incomaker\Magento2\Helper\ContactExport $contactExport,
        \Incomaker\Magento2\Helper\CategoryExport $categoryExport,
        \Incomaker\Magento2\Helper\ProductExport $productExport,
        \Incomaker\Magento2\Helper\OrderExport $orderExport
    ) {
        $this->addExport($contactExport);
        $this->addExport($categoryExport);
        $this->addExport($productExport);
        $this->addExport($orderExport);
    }

    protected function addExport(\Incomaker\Magento2\Helper\XmlExport $export) {
        $this->exports[$export::$name] = $export;
    }

    public function getExport($name) {
        return $this->exports[$name];
    }
}