<?php

namespace Incomaker\Magento2\Helper;

class ProductExport extends XmlExport {

    public static $name = "product";

    protected $products;
    protected $storeManager;
    protected $scopeConfig;
    protected $imageHelper;
    protected $stockRegistry;

    private $itemsCount;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry) {

        $this->xml = new \Magento\Framework\Simplexml\Element('<products/>');
        $this->products = $products;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->imageHelper = $imageHelper;
        $this->stockRegistry = $stockRegistry;
    }

    private $productsTree = array();

    public function prepareData(\Magento\Store\Api\Data\StoreInterface $store) {

        $productsCol = $this->products->create()
            ->addAttributeToSelect("*")
            ->setStoreId($store->getId());
        $baseCurrencyCode = $store->getBaseCurrency()->getCode();

        if ($this->getId() != NULL) {
            $productsCol->addAttributeToFilter('entity_id', array('eq' => $this->getId()));
            $this->itemsCount = 1;
        } else {
            if ($this->getSince() != NULL) $productsCol->addFieldToFilter('created_at',  array('from' => $this->getSince()));
            $productsCol->load();
            $this->itemsCount = $productsCol->count();
            $productsCol = $this->products->create()
                ->addAttributeToSelect("*")
                ->setStoreId($store->getId());
            if ($this->getLimit() != NULL) $productsCol->setPageSize($this->getLimit());
            if ($this->getOffset() != NULL) $productsCol->setCurPage($this->getOffset());
        }
        $productsCol->load();

        $localeCode = substr($this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),0,2);

        foreach ($productsCol as $product) {
            $this->productsTree[$product->getId()]["productId"] = $product->getSku();
            $this->productsTree[$product->getId()]["imageUrl"] = $this->imageHelper->init($product, 'product_base_image')->getUrl();

            $i=0;
            foreach ($product->getCategoryIds() as $categoryId) {
                $this->productsTree[$product->getId()]["categories"][$i++] = $categoryId;
            }

            $this->productsTree[$product->getId()]["currency"] = $baseCurrencyCode;
            $this->productsTree[$product->getId()]["price"] = round($product->getPrice(),
                $precision=\Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION);
            $this->productsTree[$product->getId()]["priceAfterDiscount"] = round($product->getSpecialPrice(),
                $precision=\Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION);
            $this->productsTree[$product->getId()]["purchase"] = round($product->getCost(),
                $precision=\Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION);
            $this->productsTree[$product->getId()]["stock"] = $this->stockRegistry->getStockItem($product->getId())->getQty();
            $this->productsTree[$product->getId()]["active"] = ($product->isSalable() == true ? 1 : 0);
            $this->productsTree[$product->getId()]["updated"] = $product->getCreatedAt();
            $this->productsTree[$product->getId()]["availability"] = ($product->isAvailable() == true ? 1 : 0);

            $this->productsTree[$product->getId()]["id"][$localeCode] = [
                "name" => $product->getName(),
                "description" => $product->getDescription(),
                "shortDescription" => $product->getShortDescription(),
                "url" => $product->getProductUrl()
            ];
        }
    }

    public function createXmlFeed()
    {
        $storesCol = $this->storeManager->getGroup()->getStores();  //TODO Bind store to palp
        foreach ($storesCol as $store) {
            $this->prepareData($store);
        }
        parent::createXmlFeed();

        foreach ($this->productsTree as $product) {
            $this->createProductXml($product);
        }
        return $this->xml->asXML();
    }

    protected function createProductXml($product) {
        $childXml = $this->xml->addChild('p');
        $childXml->addAttribute("id", $product["productId"]);
        $this->addItem($childXml,'imageUrl', $product["imageUrl"]);
        $categoriesXml = $childXml->addChild('categories');
        foreach ($product["categories"] as $value) {
            $categoriesXml->addChild('c', $value);
        }
        $pricesXml = $childXml->addChild('prices');         //TODO Implement multicurrency
        $pXml = $pricesXml->addChild('p');
        $pXml->addAttribute("currency", $product["currency"]);
        $this->addItem($pXml, "amount", $product["price"]);
        $this->addItem($pXml, "priceAfterDiscount", $product["priceAfterDiscount"]);
                                                                                            //TODO Implement tax and tags
        $purchaseXml = $childXml->addChild('purchase', $product["purchase"]);
        $purchaseXml->addAttribute("currency", $product["currency"]);

        $this->addItem($childXml,'stock', $product["stock"]);
        $this->addItem($childXml,'active', $product["active"]);
        $this->addItem($childXml,'updated', $product["updated"]);
        $this->addItem($childXml,'availability', $product["availability"]);

        $languagesXml = $childXml->addChild('languages');
        foreach ($product["id"] as $locale => $value) {
            $lXml = $languagesXml->addChild('l');
            $lXml->addAttribute("id", $locale);
            $this->addItem($lXml, "name", $value["name"]);
            $this->addItem($lXml, "description", $value["description"]);
            $this->addItem($lXml, "shortDescription", $value["shortDescription"]);
            $this->addItem($lXml, 'url', $value["url"]);
        }
        $this->addItem($childXml,'productId', $product["productId"]);   //TODO Deprecated: Implement variants

    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}