<?php

namespace Incomaker\Magento2\Helper;

class ProductExport extends XmlExport {

    public static $name = "product";

    protected $products;
    protected $storeManager;
    protected $scopeConfig;

    private $itemsCount;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products) {

        $this->xml = new \Magento\Framework\Simplexml\Element('<products/>');
        $this->products = $products;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    private $productsTree = array();

    public function prepareData(\Magento\Store\Api\Data\StoreInterface $store) {

        $productsCol = $this->products->create()
            ->addAttributeToSelect("*")
            ->setStoreId($store->getId());
        if ($this->getId() != NULL) {
            $productsCol->addAttributeToFilter('entity_id', array('eq' => $this->getId()));
        } else {
            if ($this->getSince() != NULL) $productsCol->addFieldToFilter('created_at',  array('from' => $this->getSince()));
            if ($this->getLimit() != NULL) $productsCol->setPageSize($this->getLimit());
            if ($this->getOffset() != NULL) $productsCol->setCurPage($this->getOffset());
        }
        $productsCol->load();
        if (empty($this->itemsCount)) {
            $this->itemsCount = $productsCol->count();
        }

        $localeCode = substr($this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),0,2);

        foreach ($productsCol as $product) {
            $this->productsTree[$product->getId()]["productId"] = $product->getSku();
            $this->productsTree[$product->getId()]["imageUrl"] = $product->getBaseImage();
            $this->productsTree[$product->getId()]["categories"] = $product->getCategories();
            $this->productsTree[$product->getId()]["price"] = $product->getPrice();
            $this->productsTree[$product->getId()]["priceAfterDiscount"] = $product->getSpecialPrice();
            $this->productsTree[$product->getId()]["stock"] = $product->getQty();
            $this->productsTree[$product->getId()]["active"] = $product->getProductOnline();
            $this->productsTree[$product->getId()]["updated"] = $product->getCreatedAt();
            $this->productsTree[$product->getId()]["availability"] = $product->getCreatedAt();
            $this->productsTree[$product->getId()]["name"] = $product->getName();
            $this->productsTree[$product->getId()]["description"] = $product->getDescription();
            $this->productsTree[$product->getId()]["url"] = $product->getUrl();
        }
    }

    public function createXmlFeed()
    {
        $storesCol = $this->storeManager->getGroup()->getStores();  //TODO Bind store to palp
        foreach ($storesCol as $store) {
            $this->prepareData($store);
        }
        parent::createXmlFeed();

        foreach ($this->productsTree as $category) {
            $this->createCategoryXml($category);
        }
        return $this->xml->asXML();
    }

    protected function createCategoryXml($category) {
        $childXml = $this->xml->addChild('p');
        $childXml->addAttribute("id", $category["productId"]);
        $this->addItem($childXml,'imageUrl', $category["imageUrl"]);
        $this->addItem($childXml,'categories', $category["categories"]);
        $this->addItem($childXml,'price', $category["price"]);
        $this->addItem($childXml,'priceAfterDiscount', $category["priceAfterDiscount"]);
        $this->addItem($childXml,'stock', $category["stock"]);
        $this->addItem($childXml,'active', $category["active"]);
        $this->addItem($childXml,'updated', $category["updated"]);
        $this->addItem($childXml,'availability', $category["availability"]);
        $this->addItem($childXml,'description', $category["description"]);
        $this->addItem($childXml,'url', $category["url"]);
        $this->addItem($childXml,'name', $category["name"]);
    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}