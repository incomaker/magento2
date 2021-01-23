<?php

namespace Incomaker\Magento2\Helper;

class ContactExport extends XmlExport {

    public static $name = "contact";

    protected $customers;
    private $itemsCount;

    public function __construct(\Magento\Customer\Model\Customer $customers) {
        $this->xml = new \Magento\Framework\Simplexml\Element('<contacts/>');
        $this->customers = $customers;
    }

    public function createXmlFeed()
    {
        $customersCol = $this->customers->getCollection()
            ->addAttributeToSelect("*")
            ->load();
        $this->itemsCount = $customersCol->count();

        parent::createXmlFeed();

        foreach ($customersCol as $customer) {
            $this->createContactXml($customer);
        }
        return $this->xml->asXML();
    }

    protected function createContactXml(\Magento\Customer\Model\Customer $customer) {
        $childXml = $this->xml->addChild('c');
        $this->addItem($childXml,'clientContactId', $customer->getId());
        $this->addItem($childXml,'sex', strtoupper($customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender'))));
        $this->addItem($childXml,'language', $customer->getStore()->getLocale());
        $this->addItem($childXml,'companyName', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getCompany()));
        $this->addItem($childXml,'firstName', htmlspecialchars($customer->getFirstname()));
        $this->addItem($childXml,'lastName', htmlspecialchars($customer->getLastname()));
        $this->addItem($childXml,'email', $customer->getEmail());
        $this->addItem($childXml,'birthday', $customer->getDob());
        $this->addItem($childXml,'street', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getStreet()));
        $this->addItem($childXml,'zipCode', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getPostcode()));
        $this->addItem($childXml,'city', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getCity()));
        $this->addItem($childXml,'phoneNumber1', $customer->getPrimaryAddress('default_billing')->getTelephone());
        $this->addItem($childXml,'phoneNumber2', $customer->getPrimaryAddress('default_billing')->getFax());
        $this->addItem($childXml,'country', strtolower($customer->getPrimaryAddress('default_billing')->getCountryId()));
        $this->addItem($childXml,'newsletter', $customer->subscriberFactory->create()->loadByCustomerId($customer->getId())->isSubscribed());
        $this->addItem($childXml,'consentTitle', 'Magento');
    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}