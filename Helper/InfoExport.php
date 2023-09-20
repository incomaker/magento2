<?php

namespace Incomaker\Magento2\Helper;

use Magento\Framework\Simplexml\Element;

class InfoExport extends XmlExport {

	public static $name = "info";

	public function __construct() {
		$this->xml = new Element('<info/>');
	}

	public function createXmlFeed() {
		parent::createXmlFeed();
		return $this->xml->asXML();
	}

	protected function getItemsCount() {
		return null;
	}

}
