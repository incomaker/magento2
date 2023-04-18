<?php
namespace Incomaker\Magento2\Observer;

use Magento\Framework\Async\CallbackDeferred;

class OrderAdd implements \Magento\Framework\Event\ObserverInterface
{
    private $incomakerApi;
    private $session;
    private $cart;
    private $proxyDeferredFactory;

    public function __construct(
        \Incomaker\Magento2\Helper\IncomakerApi $incomakerApi,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Checkout\Model\Cart $cart,
        ContactRegistration\ProxyDeferredFactory $callResultFactory
    ) {
        $this->incomakerApi = $incomakerApi;
        $this->session = $session;
        $this->cart = $cart;
        $this->proxyDeferredFactory = $callResultFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $order = $observer->getOrder();
        $this->cart->getQuote()->collectTotals();

        $this->incomakerApi->postOrderEvent('order_add', $order->getCustomerId(), $this->cart->getQuote()->getGrandTotal(), $this->cart->getQuote()->getId());
        //TODO Fix wrong posting of Total instead of orderId

        $this->session->start();
        $this->session->unsVariable();
    }
}