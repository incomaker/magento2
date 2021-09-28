<?php

namespace Incomaker\Magento2\Helper;

class ProductExport extends XmlExport
{

    public static $name = "product";

    protected $products;
    protected $storeManager;
    protected $scopeConfig;
    protected $imageHelper;
    protected $stockRegistry;
    protected $productRepository;
    protected $configure;

    private $itemsCount;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configure)
    {

        $this->xml = new \Magento\Framework\Simplexml\Element('<products/>');
        $this->products = $products;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->imageHelper = $imageHelper;
        $this->stockRegistry = $stockRegistry;
        $this->productRepository = $productRepository;
        $this->configure = $configure;
    }

    private $productsTree = array();
    private $baseCurrencyCode;
    private $localeCode;
    private $skuCache = array();

    public function prepareData(\Magento\Store\Api\Data\StoreInterface $store)
    {

        $productsCol = $this->products->create()
            ->addAttributeToSelect("*")
            ->setStoreId($store->getId());
        $this->baseCurrencyCode = $store->getBaseCurrency()->getCode();

        if ($this->getId() != NULL) {
            $productsCol->addAttributeToFilter('entity_id', array('eq' => $this->getId()));
            $this->itemsCount = 1;
        } else {
            if ($this->getSince() != NULL) $productsCol->addFieldToFilter('created_at', array('from' => $this->getSince()));
            $productsCol->load();
            $this->itemsCount = $productsCol->count();
            $productsCol = $this->products->create()
                ->addAttributeToSelect("*")
                ->setStoreId($store->getId());
            if ($this->getLimit() != NULL) $productsCol->setPageSize($this->getLimit());
            if ($this->getOffset() != NULL) $productsCol->setCurPage(($this->getOffset() / $this->getLimit())+1);
        }

        if ($productsCol->getLastPageNumber() >= ($this->getOffset() / $this->getLimit())+1) {

            $productsCol->load();

            $this->localeCode = substr($this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()), 0, 2);

            foreach ($productsCol as $product) {
                $this->addToProducTree($product);
            }
        }
    }

    protected function getSkuFromCache($productId) {

        if (!isset($this->skuCache[$productId])) {
            $this->skuCache[$productId] = $this->productRepository->getById($productId);
        }
        return $this->skuCache[$productId];
    }

    private function addToProducTree($product) {

        $masterProduct = $this->configure->getParentIdsByChild($product->getId());

        if (!empty($masterProduct) && !empty($masterProduct[0])) {
            $masterSku = mb_substr($this->getSkuFromCache($masterProduct[0])->getSku(),0,self::MAX_PRODUCT_ID_LENGTH);
        } else {
            $masterSku = mb_substr($product->getSku(),0,self::MAX_PRODUCT_ID_LENGTH);
        }

        $prod = &$this->productsTree[$product->getId()];

        $prod["variantId"] = mb_substr($product->getSku(),0,self::MAX_PRODUCT_ID_LENGTH);
        $prod["productId"] = $masterSku;
        $prod["imageUrl"] = $this->imageHelper->init($product, 'product_small_image')->getUrl();

        $i = 0;
        foreach ($product->getCategoryIds() as $categoryId) {
            $prod["categories"][$i++] = $categoryId;
        }

        $prod["currency"] = $this->baseCurrencyCode;
        $prod["price"] = round($product->getPrice(),
            $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION);
        $prod["priceAfterDiscount"] = round($product->getSpecialPrice(),
            $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION);
        $prod["purchase"] = round($product->getCost(),
            $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION);
        $prod["stock"] = $this->stockRegistry->getStockItem($product->getId())->getQty();
        $prod["active"] = ($product->isSalable() == true ? 1 : 0);
        $prod["updated"] = $product->getCreatedAt();
        $prod["availability"] = ($product->isAvailable() == true ? 1 : 0);

        $prod["id"][$this->localeCode] = ["name" => $product->getName(),
            "description" => $product->getDescription(),
            "shortDescription" => $product->getShortDescription(),
            "url" => $product->getProductUrl()];
    }

    public
    function createXmlFeed()
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

    protected
    function createProductXml($product)
    {
        $childXml = $this->xml->addChild('p');
        $childXml->addAttribute("id", $product["variantId"]);
        $this->addItem($childXml, 'imageUrl', $product["imageUrl"]);
        if (isset($product["categories"])) {
            $categoriesXml = $childXml->addChild('categories');
            foreach ($product["categories"] as $value) {
                $categoriesXml->addChild('c', $value);
            }
        }
        $pricesXml = $childXml->addChild('prices');         //TODO Implement multicurrency
        $pXml = $pricesXml->addChild('p');
        $pXml->addAttribute("currency", $product["currency"]);
        $this->addItem($pXml, "amount", $product["price"]);
        if ($product["priceAfterDiscount"] != 0) {
            $this->addItem($pXml, "priceAfterDiscount", $product["priceAfterDiscount"]);
        }
        //TODO Implement tax and tags
        $purchaseXml = $childXml->addChild('purchase', $product["purchase"]);
        $purchaseXml->addAttribute("currency", $product["currency"]);

        $this->addItem($childXml, 'stock', $product["stock"]);
        $this->addItem($childXml, 'active', $product["active"]);
        $this->addItem($childXml, 'updated', $product["updated"]);
        $this->addItem($childXml, 'availability', $product["availability"]);

        $languagesXml = $childXml->addChild('languages');
        foreach ($product["id"] as $locale => $value) {
            $lXml = $languagesXml->addChild('l');
            $lXml->addAttribute("id", $locale);
            $this->addItem($lXml, "name", $value["name"]);
            $this->addItem($lXml, "description", $value["description"]);
            $this->addItem($lXml, "shortDescription", $value["shortDescription"]);
            $this->addItem($lXml, 'url', $value["url"]);
        }
        $this->addItem($childXml, 'productId', $product["productId"]);   //TODO Deprecated: Implement variants

    }

    protected
    function getItemsCount()
    {
        return $this->itemsCount;
    }
}