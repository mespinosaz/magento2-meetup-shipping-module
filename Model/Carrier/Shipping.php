<?php

namespace Meetup\Shipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'meetup_shipping';

    /**
     * @var bool
     */
    protected $isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $items = $request->getAllItems();

        $allGuitars = array_reduce($items, function($carry, Item $item) {
            return $carry && $item->getProduct()->getAttributeSetId() == $this->getConfigData('attribute_set');
        }, true);

        if (!$allGuitars && $this->getConfigData('showmethod')) {
            return $this->buildErrorResult();
        }
        return $this->buildResult($request);


    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return float
     */
    private function calculateShippingPrice(RateRequest $request)
    {
        return $this->getConfigData('price') * $request->getPackageQty();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error
     */
    private function buildErrorResult()
    {
        $resultError = $this->_rateErrorFactory->create();
        $resultError->setCarrier($this->_code);
        $resultError->setCarrierTitle($this->getConfigData('title'));
        $resultError->setErrorMessage($this->getConfigData('specificerrmsg'));

        return $resultError;
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    private function buildResult(RateRequest $request)
    {
        $price = $this->calculateShippingPrice($request);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->getConfigData('name'));
        $method->setMethodTitle();

        $method->setPrice($price);
        $method->setCost($price);

        $result->append($method);

        return $result;
    }
}
