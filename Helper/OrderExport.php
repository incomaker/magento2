<?php

namespace Incomaker\Magento2\Helper;

use Magento\Framework\Simplexml\Element;

class OrderExport extends XmlExport
{

    public static $name = "order";

    protected $orders;

    private $itemsCount;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orders)
    {

        $this->xml = new Element('<orders/>');
        $this->orders = $orders;
    }

    public function createXmlFeed()
    {
        $ordersCol = $this->orders
            ->create()
            ->addFieldToSelect('*');
        if ($this->getId() != NULL) {
            $ordersCol->addAttributeToFilter('entity_id', array('eq' => $this->getId()));
            $this->itemsCount = 1;
        } else {
            if ($this->getSince() != NULL) $ordersCol->addFieldToFilter('created_at', array('from' => $this->getSince()));
            $ordersCol->load();
            $this->itemsCount = $ordersCol->count();
            $ordersCol = $this->orders
                ->create()
                ->addFieldToSelect('*');
            if ($this->getLimit() != NULL) $ordersCol->setPageSize($this->getLimit());
            if ($this->getOffset() != NULL) $ordersCol->setCurPage($this->getOffset() / $this->getLimit());
        }
        $ordersCol->load();

        parent::createXmlFeed();

        if ($ordersCol->getLastPageNumber() >= $this->getOffset() / $this->getLimit()) {

            foreach ($ordersCol as $order) {
                $this->createOrderXml($order);
            }
        }
        return $this->xml->asXML();
    }

    protected function createOrderXml($order)
    {
        $childXml = $this->xml->addChild('o');
        $childXml->addAttribute("id", $order->getId());
        if ($order->getCustomerId() == NULL) {
            $contact = $childXml->addChild('contact');
            $this->addItem($contact, 'firstName', htmlspecialchars($order->getCustomerFirstname()));
            $this->addItem($contact, 'lastName', htmlspecialchars($order->getCustomerLastname()));
            $this->addItem($contact, 'email', $order->getCustomerEmail());
            if ($order->getBillingAddress() != null) {
                $this->addItem($contact, 'street', htmlspecialchars($order->getBillingAddress()->getStreet()[0]));
                $this->addItem($contact, 'city', htmlspecialchars($order->getBillingAddress()->getCity()));
                $this->addItem($contact, 'zipCode', htmlspecialchars($order->getBillingAddress()->getPostcode()));
                $this->addItem($contact, 'phoneNumber1', $order->getBillingAddress()->getTelephone());
                $this->addItem($contact, 'phoneNumber2', $order->getBillingAddress()->getFax());
                $this->addItem($contact, 'country', strtolower($order->getBillingAddress()->getCountryId()));
            }
        } else {
            $this->addItem($childXml, 'contactId', $order->getCustomerId());
        }

        $this->addItem($childXml, 'created', $order->getCreatedAt());
        $this->addItem($childXml, 'state', $order->getState());
        $items = $childXml->addChild('items');
        //$order = $this->orderRepository->get($orderId);
        if ($order->getAllItems() != null) {
            foreach ($order->getAllItems() as $itm) {
                $item = $items->addChild('i');
                $item->addAttribute("id", mb_substr($itm->getSku(), 0, self::MAX_PRODUCT_ID_LENGTH));
                $this->addItem($item, "variantId", substr($itm->getSku(), 0, self::MAX_PRODUCT_ID_LENGTH));     //TODO Deprecated: will be removed in next version
                $this->addItem($item, "quantity", $itm->getQtyOrdered());
                $price = $this->addItem($item, "price", $itm->getPrice());
                $price->addAttribute("currency", $order->getOrderCurrencyCode());
            }
        }
    }

    protected function getItemsCount()
    {
        return $this->itemsCount;
    }
}