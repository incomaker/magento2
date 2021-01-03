<?php

namespace Incomaker\Magento2\Helper;

abstract class XmlExport {

    const PRODUCT_ATTRIBUTE = 100000;
    const MAX_LIMIT = 1000;

    public static $name;

    protected $xml;
    protected $limit;
    protected $offset;
    protected $id;
    protected $since;

    protected $numberOfLanguages;
    protected $shopId;

    public function getNumberOfLanguages()
    {
        return $this->numberOfLanguages;
    }

    public function setNumberOfLanguages($numberOfLanguages)
    {
        $this->numberOfLanguages = $numberOfLanguages;
    }

    public function getShopId()
    {
        return $this->shopId;
    }

    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    public function setApiKey($apiKey)
    {
        if (!isset($apiKey)) {
            throw new UnexpectedValueException();
        }
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit) {

        if (($limit != NULL) && (!ctype_digit($limit))) {
            throw new InvalidArgumentException("Limit must be a number.");
        }
        $this->limit = $limit;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset($offset) {

        if (($offset != NULL) && (!ctype_digit($offset))) {
            throw new InvalidArgumentException("Offset must be a number.");
        }
        $this->offset = $offset;
        if (empty($this->limit)) {
            $this->limit = self::MAX_LIMIT;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id) {
        if (($id != NULL) && (!ctype_digit($id))) {
            throw new InvalidArgumentException("Offset must be a number.");
        }
        $this->id = $id;
    }

    public function getSince()
    {
        return $this->since;
    }

    public function setSince($since) {

        if (isset($since)) {
            $tempDate = explode('-', $since);
            if ((count($tempDate) != 3) || !checkdate($tempDate[1], $tempDate[2], $tempDate[0])) {
                throw new InvalidArgumentException("Date must be in YYYY-MM-DD format");
            }
        }

        $this->since = $since;
    }


    public function createXmlFeed()
    {
        $this->xml->addAttribute('totalItems', $this->itemsCount());

//        $this->numberOfLanguages = count(Language::getLanguages(false, Shop::getContextShopID(), true));
//        $this->shopId = Shop::getContextShopID();
//        $this->shopId = Shop::getContextShopID();
    }
}
