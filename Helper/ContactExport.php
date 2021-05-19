<?php

namespace Incomaker\Magento2\Helper;

class ContactExport extends XmlExport {

    public static $name = "contact";

    protected $customers;
    protected $subscribers;

    private $itemsCount;

    public function __construct(
        \Magento\Customer\Model\Customer $customers,
        \Magento\Newsletter\Model\Subscriber $subscribers) {

        $this->xml = new \Magento\Framework\Simplexml\Element('<contacts/>');
        $this->customers = $customers;
        $this->subscribers = $subscribers;
    }

    public function createXmlFeed()
    {
        $customersCol = $this->customers->getCollection()
            ->addAttributeToSelect("*");
        if ($this->getId() != NULL) {
            $customersCol->addAttributeToFilter('entity_id', array('eq' => $this->getId()));
            $this->itemsCount = 1;
        } else {
            if ($this->getSince() != NULL) $customersCol->addFieldToFilter('created_at',  array('from' => $this->getSince()));
            $customersCol->load();
            $this->itemsCount = $customersCol->count();
            $customersCol = $this->customers->getCollection()
                ->addAttributeToSelect("*");
            if ($this->getLimit() != NULL) $customersCol->setPageSize($this->getLimit());
            if ($this->getOffset() != NULL) $customersCol->setCurPage($this->getOffset());
        }
        $customersCol->load();

        parent::createXmlFeed();

        foreach ($customersCol as $customer) {
            $this->createContactXml($customer);
        }
        return $this->xml->asXML();
    }

    protected function createContactXml(\Magento\Customer\Model\Customer $customer) {
        $childXml = $this->xml->addChild('c');
        $childXml->addAttribute("id", $customer->getId());
        $this->addItem($childXml,'sex', strtoupper($customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender'))));
        $this->addItem($childXml,'language', $customer->getStore()->getLocale());
        $this->addItem($childXml,'companyName', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getCompany()));
        $this->addItem($childXml,'firstName', htmlspecialchars($customer->getFirstname()));
        $this->addItem($childXml,'lastName', htmlspecialchars($customer->getLastname()));
        $this->addItem($childXml,'email', $customer->getEmail());
        $this->addItem($childXml,'birthday', $customer->getDob());
        if (isset($customer->getPrimaryAddress('default_billing')->getStreet()[0])) {
            $this->addItem($childXml,'street', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getStreet()[0]));
        }
        $this->addItem($childXml,'zipCode', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getPostcode()));
        $this->addItem($childXml,'city', htmlspecialchars($customer->getPrimaryAddress('default_billing')->getCity()));
        $this->addItem($childXml,'phoneNumber1', $customer->getPrimaryAddress('default_billing')->getTelephone());
        $this->addItem($childXml,'phoneNumber2', $customer->getPrimaryAddress('default_billing')->getFax());
        $this->addItem($childXml,'country', strtolower($customer->getPrimaryAddress('default_billing')->getCountryId()));
        $this->addItem($childXml,'newsletter', $this->subscribers->loadByCustomerId($customer->getId())->isSubscribed()?1:0);
        $this->addItem($childXml,'consentTitle', 'Magento');
    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}