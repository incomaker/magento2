<?php

namespace Incomaker\Magento2\Controller\Data\Export;

class ContactExport extends XmlExport {

    public static $name = "contact";

    protected $customers;

    public function __construct(\Magento\Customer\Model\Customer $customers) {
        $this->xml = new \Magento\Framework\Simplexml\Element('<contacts/>');
        $this->customers = $customers;
    }

    public function createXmlFeed()
    {
        parent::createXmlFeed();

        $this->customers->getCollection()
            ->addAttributeToSelect("*")
            ->load();
    }

    protected function itemsCount() {
        return 0;
    }
}