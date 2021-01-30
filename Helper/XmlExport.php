<?php

namespace Incomaker\Magento2\Helper;

abstract class XmlExport {

    const PRODUCT_ATTRIBUTE = 100000;
    const MAX_LIMIT = 1000;
    const API_VERSION = "2.8";

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
        $this->offset = $offset + 1;
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
            if (!preg_match("/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))$/",$since)) {
                throw new InvalidArgumentException("Date must be in YYYY-MM-DD format");
            }
        }

        $this->since = $since;
    }

    protected function addItem($object, $id, $item) {
        if (isset($item)) {
            return $object->addChild($id, htmlspecialchars($item));
        }
    }

    public function createXmlFeed()
    {
        $this->xml->addAttribute('version', $this::API_VERSION);
        $this->xml->addAttribute('totalItems', $this->getItemsCount());
    }
}
