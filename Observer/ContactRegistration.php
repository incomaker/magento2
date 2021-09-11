<?php
namespace Incomaker\Magento2\Observer;

class ContactRegistration implements \Magento\Framework\Event\ObserverInterface
{

    private $incomakerApi;
    private $proxyDeferredFactory;

    public function __construct(
        \Incomaker\Magento2\Helper\IncomakerApi $incomakerApi,
        ContactRegistration\ProxyDeferredFactory $callResultFactory
    ) {
        $this->incomakerApi = $incomakerApi;
        $this->proxyDeferredFactory = $callResultFactory ?? ObjectManager::getInstance()->get(ProxyDeferredFactory::class);;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $customer = $observer->getData('customer');
        $this->incomakerApi->addContact($customer);
        $this->incomakerApi->postEvent("register", $customer);
/*        $this->proxyDeferredFactory->create(
            [
                'deferred' => new CallbackDeferred(
                    function () use ($customer) {
                        $this->incomakerApi->addContact($customer);
                        $this->incomakerApi->postEvent("register", $customer);
                    }
                )
            ]
        );*/
    }
}