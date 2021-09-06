<?php

namespace Incomaker\Magento2\Helper;

class IncomakerApi
{
    private $cookieManager;
    private $eventController;
    private $api;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Incomaker\Api\Connector $api
    ) {
        $this->cookieManager = $cookieManager;
        $this->api = $api;
    }

    public function postEvent($event, $customer, /*$product = NULL,*/ $session = NULL) {

        $event = new \Incomaker\Api\Objects\Event($event, $this->cookieManager->getCookie("incomaker_p"));
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
        $this->eventController->addEvent($event);
    }

    public function addContact($customer) {

    }
}