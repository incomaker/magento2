<?php


namespace Incomaker\Magento2\Helper;


class ExportManager
{

    protected $exports;

    public function __construct(
        \Incomaker\Magento2\Helper\ContactExport $contactExport
    ) {
        $this->exports[$contactExport::$name] = $contactExport;
    }

    public function getExport($name) {
        return $this->exports[$name];
    }
}