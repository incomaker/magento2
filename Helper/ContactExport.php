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
            if ($this->getOffset() != NULL) $customersCol->setCurPage($this->getOffset() / $this->getLimit());
        }
        if ($customersCol->getLastPageNumber() >= $this->getOffset() / $this->getLimit()) {

            $customersCol->load();

            parent::createXmlFeed();

            foreach ($customersCol as $customer) {
                $this->createContactXml($customer);
            }
        }
        return $this->xml->asXML();
    }

    protected function createContactXml(\Magento\Customer\Model\Customer $customer) {
        $childXml = $this->xml->addChild('c');
        $billingAddress = $customer->getDefaultBillingAddress();
        $childXml->addAttribute("id", $customer->getId());
        $gender = strtoupper($customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender')));
        if (!empty($gender)) $this->addItem($childXml,'sex', $gender);
        $this->addItem($childXml,'language', $customer->getStore()->getLocale());
        $this->addItem($childXml,'firstName', htmlspecialchars($customer->getFirstname()));
        $this->addItem($childXml,'lastName', htmlspecialchars($customer->getLastname()));
        $this->addItem($childXml,'email', $customer->getEmail());
        $this->addItem($childXml,'birthday', $customer->getDob());
        if ($billingAddress != false) {
            $this->addItem($childXml,'companyName', htmlspecialchars($billingAddress->getCompany()));
            if (isset($billingAddress->getStreet()[0])) {
                $this->addItem($childXml,'street', htmlspecialchars($billingAddress->getStreet()[0]));
            }
            $this->addItem($childXml,'zipCode', htmlspecialchars($billingAddress->getPostcode()));
            $this->addItem($childXml,'city', htmlspecialchars($billingAddress->getCity()));
            $this->addItem($childXml,'phoneNumber1', $billingAddress->getTelephone());
            $this->addItem($childXml,'phoneNumber2', $billingAddress->getFax());
            $this->addItem($childXml,'country', strtolower($billingAddress->getCountryId()));
        }
        $this->addItem($childXml,'newsletter', $this->subscribers->loadByCustomerId($customer->getId())->isSubscribed()?1:0);
        $this->addItem($childXml,'consentTitle', 'Magento');
    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}