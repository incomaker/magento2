<?php
namespace Incomaker\Magento2\Controller\Data;

use Incomaker\Magento2\Controller\Data\Export\XmlExport;
use Incomaker\Magento2\Controller\Data\Export\ContactExport;
use Magento\Framework\UrlFactory;

class Feed extends \Magento\Framework\App\Action\Action
{

    protected $scopeConfig;
    protected $resultRawFactory;
    protected $manager;

    protected $xmlExport;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Incomaker\Magento2\Helper\ExportManager $manager
    )
    {
        $this->resultRawFactory = $resultRawFactory;
        $this->scopeConfig = $scopeConfig;
        $this->manager = $manager;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $result = $this->resultRawFactory->create();

        try {
            $xmlExport = $this->manager->getExport($params["type"]);
        } catch (\Exception $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST);
            $result->setContents('400-1 Invalid command');
            return $result;
        }

        try {
            $xmlExport->setApiKey($this->scopeConfig->getValue('incomaker/settings/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        } catch (Exception $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_UNAUTHORIZED);
            $result->setContents("401-2 Invalid API key");
            return $result;
        }

        try {
            $xmlExport->setLimit(isset($params["limit"]) ? $params["limit"] : NULL);
            $xmlExport->setOffset(isset($params["offset"]) ? $params["offset"] : NULL);
            $xmlExport->setId(isset($params["id"]) ? $params["id"] : NULL);
            $xmlExport->setSince(isset($params["since"]) ? $params["since"] : NULL);
        } catch (InvalidArgumentException $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST);
            $result->setContents("400-2 " . $e->getMessage());
            return $result;
        }

        $result->setHeader('Content-Type', 'text/xml');
        try {
            $result->setContents($xmlExport->createXmlFeed());
        } catch (Exception $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR);
            $result->setContents("510-1 " . $e->getMessage());
        }

        return $result;
    }
}
?>