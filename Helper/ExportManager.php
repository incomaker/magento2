<?php

namespace Incomaker\Magento2\Helper;

class ExportManager {

	protected $exports;

	public function __construct(
		ContactExport $contactExport,
		CategoryExport $categoryExport,
		ProductExport $productExport,
		OrderExport $orderExport,
		CouponExport $couponExport,
		InfoExport $infoExport
	) {
		$this->addExport($contactExport);
		$this->addExport($categoryExport);
		$this->addExport($productExport);
		$this->addExport($orderExport);
		$this->addExport($couponExport);
		$this->addExport($infoExport);
	}

	protected function addExport(XmlExport $export) {
		$this->exports[$export::$name] = $export;
	}

	public function getExport($name) {
		return $this->exports[$name];
	}

	public function exportExists($name): bool {
		return isset($this->exports[$name]);
	}
}
