<?php
namespace Incomaker\Magento2\Observer;

use Magento\Framework\Async\CallbackDeferred;

class CartUpdate implements \Magento\Framework\Event\ObserverInterface
{
    private $incomakerApi;
    private $customerSession;
    private $cart;
    private $session;
    private $proxyDeferredFactory;

    public function __construct(
        \Incomaker\Magento2\Helper\IncomakerApi $incomakerApi,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Session\SessionManagerInterface $session,
        CartUpdate\ProxyDeferredFactory $callResultFactory
    ) {
        $this->incomakerApi = $incomakerApi;
        $this->customerSession = $customerSession;
        $this->session = $session;
        $this->cart = $cart;
        $this->proxyDeferredFactory = $callResultFactory ?? ObjectManager::getInstance()->get(ProxyDeferredFactory::class);;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $this->session->start();
        $item = $observer->getEvent()->getData('product');
        $customer = $this->customerSession->getCustomer();

        $new = array();
        foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
            $new[] = $item['product_id'];
        }

        $old = unserialize($this->session->getVariable());

        if (empty($old)) $old = array();
        $diff = array_diff($new, $old);

        if (!empty($diff)) {
            $this->incomakerApi->postProductEvent('cart_add', $customer, current($diff), $this->cart->getQuote()->getId());
        } else {
            $diff = array_diff($old, $new);
            if (!empty($diff)) {
                $this->incomakerApi->postProductEvent('cart_remove', $customer, current($diff), $this->cart->getQuote()->getId());
            }
        }
        $this->session->setVariable(serialize($new));
    }
}