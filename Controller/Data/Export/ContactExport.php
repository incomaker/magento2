<?php

namespace Incomaker\Magento2\Controller\Data\Export;

class ContactExport extends XmlExport {

    public static $name = "contact";

    public function __construct() {
        $this->xml = new SimpleXMLElement('<contacts/>');
    }


}