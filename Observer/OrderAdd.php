<?php
namespace Incomaker\Magento2\Observer;

use Magento\Framework\Async\CallbackDeferred;

class OrderAdd implements \Magento\Framework\Event\ObserverInterface
{
    private $incomakerApi;
    private $session;
    private $cart;

    public function __construct(
        \Incomaker\Magento2\Helper\IncomakerApi $incomakerApi,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Checkout\Model\Cart $cart,
        ContactRegistration\ProxyDeferredFactory $callResultFactory
    ) {
        $this->incomakerApi = $incomakerApi;
        $this->session = $session;
        $this->cart = $cart;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $order = $observer->getOrder();
        $this->cart->getQuote()->collectTotals();

        $this->incomakerApi->postOrderEvent('order_add', $order->getCustomerId(), $this->cart->getQuote()->getGrandTotal(), $this->cart->getQuote()->getId());

        $this->session->start();
        $this->session->unsVariable();
    }
}