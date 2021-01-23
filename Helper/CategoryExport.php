<?php

namespace Incomaker\Magento2\Helper;

class ContactExport extends XmlExport {

    public static $name = "category";

    protected $categories;
    private $itemsCount;

    public function __construct(\Magento\Customer\Model\Category $categories) {
        $this->xml = new \Magento\Framework\Simplexml\Element('<categories/>');
        $this->categories = $categories;
    }

    public function createXmlFeed()
    {
        $categoriesCol = $this->categories->getCollection()
            ->addAttributeToSelect("*")
            ->load();
        $this->itemsCount = $categoriesCol->count();

        parent::createXmlFeed();

        foreach ($categoriesCol as $category) {
            $this->createContactXml($category);
        }
        return $this->xml->asXML();
    }

    protected function createContactXml(\Magento\Customer\Model\Category $category) {
        $childXml = $this->xml->addChild('c');
        $this->addItem($childXml,'clientContactId', $category->getId());
    }

    protected function getItemsCount() {
        return $this->itemsCount;
    }
}