<?php

namespace Incomaker\Magento2\Helper;

class IncomakerApi
{
    private $cookieManager;
    private $eventController;
    private $api;
    private $logger;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Incomaker\Api\Connector $api,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->cookieManager = $cookieManager;
        $this->api = $api;
        $this->logger = $logger;
    }

    public function getPermId()
    {

        if ($this->cookieManager->getCookie("incomaker_p") != null) {
            return $this->cookieManager->getCookie("incomaker_p");
        }
        return $this->cookieManager->getCookie("permId");
    }

    public function postEvent($event, $customer, /*$product = NULL,*/ $session = NULL)
    {
        $this->logger->info('1');
        $event = new \Incomaker\Api\Objects\Event($event, $this->getPermId());
        /*        if (!empty($product)) {
                    $event->setRelatedId(current($product));
                }*/
        if (!empty($session)) {
            $event->setSessionId($session);
        }
        if (isset($customer)) {
            $event->setContactId($customer->getId());
        }
        if (!isset($this->eventController)) {
            $this->eventController = $this->incomaker->createEventController();
        }
        $this->logger->info('2');
        $this->eventController->addEvent($event);
        $this->logger->info('3');
    }

    public function addContact($customer)
    {

        $this->contactController = $this->incomaker->createContactController();

        $contact = new \Incomaker\Api\Objects\Contact($customer->getId());
        $contact->setPermId($this->getPermId());
        $contact->setFirstName(htmlspecialchars($customer->getFirstname()));
        $contact->setLastName(htmlspecialchars($customer->getLastname()));
        $contact->setEmail($customer->getEmail());
        $contact->setBirthday($customer->getDob());

        $billingAddress = $customer->getDefaultBillingAddress();
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

        $this->contactController->addContact($contact);

    }
}