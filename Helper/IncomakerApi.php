<?php

namespace Incomaker\Magento2\Helper;

class IncomakerApi
{
    private $cookieManager;
    private $eventController;
    private $addressRepository;
    private $incomaker;
    private $logger;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Incomaker\Api\Connector $api,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->cookieManager = $cookieManager;
        $this->incomaker = $api;
        $this->addressRepository = $addressRepository;
        $this->logger = $logger;
    }

    public function getPermId()
    {

        if ($this->cookieManager->getCookie("incomaker_p") != null) {
            return $this->cookieManager->getCookie("incomaker_p");
        }
        return $this->cookieManager->getCookie("permId");
    }

    public function getCampaignId() {
        return $this->cookieManager->getCookie("incomaker_c");
    }

    public function postEvent($event, $customer)
    {
        $event = new \Incomaker\Api\Data\Event($event, $this->getPermId());

        if (isset($customer)) {
            $event->setContactId($customer->getId());
        }
        if (!isset($this->eventController)) {
            $this->eventController = $this->incomaker->createEventController();
        }
        $this->eventController->addEvent($event);
    }

    public function postProductEvent($event, $customer, $product, $session)
    {
        $event = new \Incomaker\Api\Data\Event($event, $this->getPermId());

        if (isset($customer)) {
            $event->setContactId($customer->getId());
        }
        if (!empty($product)) {
            $event->setRelatedId($product);
        }
        if (!empty($session)) {
            $event->setSessionId($session);
        }
        if (!isset($this->eventController)) {
            $this->eventController = $this->incomaker->createEventController();
        }
        $this->eventController->addEvent($event);
    }

    public function postOrderEvent($event, $customer, $total, $session)
    {
        $event = new \Incomaker\Api\Data\Event($event, $this->getPermId());

        if (isset($customer)) {
            $event->setContactId($customer);
        }
        $event->setCampaignId($this->getCampaignId());  //TODO remove passing campaignId this way
        $event->addCustomField("total", $total);
        if (!empty($session)) {
            $event->setSessionId($session);
        }
        if (!isset($this->eventController)) {
            $this->eventController = $this->incomaker->createEventController();
        }
        $this->eventController->addEvent($event);
    }

    public function addContact(\Magento\Customer\Model\Data\Customer $customer)
    {

        $this->contactController = $this->incomaker->createContactController();

        $contact = new \Incomaker\Api\Data\Contact($customer->getId());
        $contact->setPermId($this->getPermId());
        $contact->setFirstName(htmlspecialchars($customer->getFirstname()));
        $contact->setLastName(htmlspecialchars($customer->getLastname()));
        $contact->setEmail($customer->getEmail());
        $contact->setBirthday($customer->getDob());

        $customerId = $customer->getDefaultBilling();
        if (isset($customerId)) {
            $billingAddress = $this->addressRepository->getById($customerId);
            if ($billingAddress != false) {
                $contact->setCompanyName(htmlspecialchars($billingAddress->getCompany()));
                if (isset($billingAddress->getStreet()[0])) {
                    $contact->setStreet(htmlspecialchars($billingAddress->getStreet()[0]));
                }

                $contact->setZipCode(htmlspecialchars($billingAddress->getPostcode()));
                $contact->setCity(htmlspecialchars($billingAddress->getCity()));
                $contact->setPhoneNumber1($billingAddress->getTelephone());
                $contact->setPhoneNumber2($billingAddress->getFax());
                $contact->setCountry(strtolower($billingAddress->getCountryId()));
            }
        }

        $this->contactController->addContact($contact);

    }
}