<?php
namespace Incomaker\Magento2\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ContactRegistration implements ObserverInterface
{

    private $incomakerApi;

    public function __constructor(
        \Incomaker\Magento2\Helper\IncomakerApi $incomakerApi
    ) {
        $this->incomakerApi = $incomakerApi;
    }

    public function execute(Observer $observer) {

        $customer = $observer->getData('customer');
        $this->proxyDeferredFactory->create(
            [
                'deferred' => new CallbackDeferred(
                    function () use ($customer) {
                        $this->incomakerApi->addContact($customer);
                        $this->incomakerApi->postEvent("register", $customer);
                    }
                )
            ]
        );
    }
}