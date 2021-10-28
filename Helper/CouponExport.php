<?php

namespace Incomaker\Magento2\Helper;

class CouponExport extends XmlExport
{

    public static $name = "coupon";

    protected $coupons;
    protected $rules;
    protected $generator;
    protected $configuration;

    private $itemsCount;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $coupons,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory   $rules,
        \Magento\SalesRule\Model\CouponGenerator                        $generator,
        Configuration                                                   $configuration
    )
    {
        $this->xml = new \Magento\Framework\Simplexml\Element('<coupons/>');
        $this->coupons = $coupons;
        $this->rules = $rules;
        $this->generator = $generator;
        $this->configuration = $configuration;
    }

    public function generateCoupons($rules)
    {
        if (!isset($rules->getData()[0])) return;

        if (($this->getGenerate() != NULL) && ($this->getId() != NULL)) {
            return $this->generator->generateCodes(array(
                'rule_id' => $rules->getData()[0]['rule_id'],
                'qty' => $this->getGenerate(),
                'length' => $this->configuration->getConfig('incomaker/auto_generated_coupon_codes/length', 12),
                'format' => $this->configuration->getConfig('incomaker/auto_generated_coupon_codes/format', 'alphanum'),
                'prefix' => $this->configuration->getConfig('incomaker/auto_generated_coupon_codes/prefix', ''),
                'suffix' => $this->configuration->getConfig('incomaker/auto_generated_coupon_codes/suffix', ''),
                'dash' => $this->configuration->getConfig('incomaker/auto_generated_coupon_codes/dash', ''),
            ));
        }
    }

    public function createXmlFeed()
    {
        $now = new \DateTime();
        $rules = $this->rules->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('coupon_type', 2)
            ->addFieldToFilter(array('to_date', 'to_date'), array(array('gteq' => $now->format('Y-m-d H:i:s')), array('null' => "null")));
        if ($this->getId() != NULL) {
            $rules->addFieldToFilter('name', array('eq' => $this->getId()));
        }
        $this->itemsCount = $rules->count();
        $rules->load();

        $codes = $this->generateCoupons($rules);

        parent::createXmlFeed();

        foreach ($rules->getData() as $rule) {
            $this->createCouponXml($rule, $codes);
        }

        return $this->xml->asXML();
    }

    protected function createCouponXml($rule, $codes = NULL)
    {
        $childXml = $this->xml->addChild('c');
        $childXml->addAttribute("id", $rule['name']);
        $this->addItem($childXml, 'reusable', $rule['use_auto_generation'] == 0 ? 1 : 0);
        $this->addItem($childXml, 'discountType', $rule['simple_action'] == "by_percent" ? "PERCENTUAL" : "MONETARY");
        $this->addItem($childXml, 'discount', $rule['discount_amount']);
        $this->addItem($childXml, 'validFrom', $rule['from_date']);
        $this->addItem($childXml, 'validTo', $rule['to_date']);
        $valuesXml = $childXml->addChild('values');
        if ($rule['use_auto_generation'] == 1) {

            $coupons = $this->coupons->create()
                ->addFieldToFilter('rule_id', $rule['rule_id']);
            $coupons->load();

            if ($codes == NULL) {
                foreach ($coupons->getData() as $coupon) {
                    if ($coupon["usage_limit"] > $coupon["times_used"]) {
                        $this->addItem($valuesXml, "v", $coupon['code']);
                    }
                }
            } else {
                foreach ($codes as $code) {
                    $this->addItem($valuesXml, "v", $code);
                }
            }
        } else {
            $this->addItem($valuesXml, "v", $rule['code']);
        }
    }

    protected function getItemsCount()
    {
        return $this->itemsCount;
    }
}