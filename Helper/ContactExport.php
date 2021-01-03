<?php

namespace Incomaker\Magento2\Helper;

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

        $customersCol = $this->customers->getCollection()
            ->addAttributeToSelect("*")
            ->load();

        foreach ($customersCol as $customer) {
            $this->createContactXml($customer);
        }
        return $this->xml->asXML();
    }

    protected function createContactXml(\Magento\Customer\Model\Customer $customer) {
        $childXml = $this->xml->addChild('c');
        $this->addItem($childXml,'clientContactId', $customer->getId());
        $this->addItem($childXml,'sex', $customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender')));

        $this->addItem($childXml,'language', $customer->getStore()->getLocale());
        $this->addItem($childXml,'companyName', htmlspecialchars($this->getPrimaryAddress('default_billing')->getCompany()));
        $this->addItem($childXml,'firstName', htmlspecialchars($customer->getFirstname()));
        $this->addItem($childXml,'lastName', htmlspecialchars($customer->getLastName()));
        $this->addItem($childXml,'email', $customer->getEmail());
        $this->addItem($childXml,'birthday', $customer->getDob());
        $this->addItem($childXml,'street', htmlspecialchars($this->getPrimaryAddress('default_billing')->getStreet()));
        $this->addItem($childXml,'zipCode', htmlspecialchars($this->getPrimaryAddress('default_billing')->getPostcode()));
        $this->addItem($childXml,'city', htmlspecialchars($this->getPrimaryAddress('default_billing')->getCity()));
        $this->addItem($childXml,'phoneNumber1', $this->getPrimaryAddress('default_billing')->getTelephone());
        $this->addItem($childXml,'phoneNumber2', $this->getPrimaryAddress('default_billing')->getFax());
        $this->addItem($childXml,'country', $this->getPrimaryAddress('default_billing')->getCountryId());
        $this->addItem($childXml,'newsletter', $this->subscriberFactory->create()->loadByCustomerId($customer->getId())->isSubscribed());
        $this->addItem($childXml,'consentTitle', 'Magento');
    }

    protected function itemsCount() {
        return 0;
    }
}