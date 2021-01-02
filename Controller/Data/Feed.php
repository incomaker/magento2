<?php
namespace Incomaker\Magento2\Controller\Data;

use Incomaker\Magento2\Controller\Data\Export\XmlExport;
use Incomaker\Magento2\Controller\Data\Export\ContactExport;
use Magento\Framework\UrlFactory;

class Feed extends \Magento\Framework\App\Action\Action
{

    protected $scopeConfig;
    protected $resultRawFactory;
    protected $exports;

    protected $xmlExport;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->resultRawFactory = $resultRawFactory;
        $this->exports[ContactExport::$name] = ContactExport::class;
//        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $result = $this->resultRawFactory->create();

        try {
            $xmlExport = new $this->exports[$params["type"]]();
        } catch (\Exception $e) {
            $result->setHeader('HTTP/1.0 400 Bad Request');
            echo "400-1 Invalid command";
            die();
        }

        try {
            $xmlExport->setApiKey($this->scopeConfig->getValue('incomaker/settings/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        } catch (Exception $e) {
            $result->setHeader('HTTP/1.0 401 Unauthorized');
            echo "401-2 Invalid API key";
            die();
        }

        try {
            $xmlExport->setLimit(isset($params["limit"]) ? $params["limit"] : NULL);
            $xmlExport->setOffset(isset($params["offset"]) ? $params["offset"] : NULL);
            $xmlExport->setId(isset($params["id"]) ? $params["id"] : NULL);
            $xmlExport->setSince(isset($params["since"]) ? $params["since"] : NULL);
        } catch (InvalidArgumentException $e) {
            $result->setHeader('HTTP/1.0 400 Bad Request');
            echo "400-2 " . $e->getMessage();
            die();
        }

        $result->setHeader('Content-Type', 'text/xml');
        try {
            $result->setContents($xmlExport->createXmlFeed());
        } catch (Exception $e) {
            $result->setHeader('HTTP/1.0 510 Not extended');
            echo "510-1 " . $e->getMessage();
            die();
        }

        return $result;
    }
}
?>