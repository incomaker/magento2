<?php

namespace Incomaker\Magento2\Helper;

class CategoryExport extends XmlExport {

    public static $name = "category";

    protected $categories;
    protected $storeManager;
    protected $scopeConfig;

    private $itemsCount;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categories) {

        $this->xml = new \Magento\Framework\Simplexml\Element('<categories/>');
        $this->categories = $categories;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    private $categoriesTree = array();

    public function prepareData(\Magento\Store\Api\Data\StoreInterface $store) {

        $categoriesCol = $this->categories->create()
            ->addAttributeToSelect("*")
            ->setStoreId($store->getId());
        if ($this->getId() != NULL) {
            $categoriesCol->addAttributeToFilter('entity_id', array('eq' => $this->getId()));
        } else {
            if ($this->getSince() != NULL) $categoriesCol->addFieldToFilter('created_at',  array('from' => $this->getSince()));
            if ($this->getLimit() != NULL) $categoriesCol->setPageSize($this->getLimit());
            if ($this->getOffset() != NULL) $categoriesCol->setCurPage($this->getOffset());
        }
        $categoriesCol->load();
        if (empty($this->itemsCount)) {
            $this->itemsCount = $categoriesCol->count();
        }

        $localeCode = substr($this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),0,2);

        foreach ($categoriesCol as $category) {
            $this->categoriesTree[$category->getId()]["categoryId"] = $category->getId();
            $this->categoriesTree[$category->getId()]["parentId"] = $category->getParentId();
            $this->categoriesTree[$category->getId()]["lang"][$localeCode] = [
                "name" => $category->getName(),
                "url" => $category->getUrl()
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

        foreach ($this->categoriesTree as $category) {
            $this->createCategoryXml($category);
        }
        return $this->xml->asXML();
    }

    protected function createCategoryXml($category) {
        $childXml = $this->xml->addChild('c');
        $this->addItem($childXml,'categoryId', $category["categoryId"]);
        $this->addItem($childXml,'parentCategoryId', $category["parentId"]);
        $languagesXml = $childXml->addChild('languages');
        foreach ($category["lang"] as $locale => $value) {
            $langXml = $languagesXml->addChild('l');
            $langXml->addAttribute("lang", $locale);
            $this->addItem($langXml, "name", $value["name"]);
            $this->addItem($langXml, "url", $value["url"]);
        }
    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}