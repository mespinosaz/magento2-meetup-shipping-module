<?php

namespace Meetup\Shipping\Block\Product;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Meetup\Shipping\Model\Carrier\Shipping as ShippingModel;

class Shipping extends Template
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var ShippingModel
     */
    private $shippingModel;

    /**
     * @param Template\Context $context
     * @param array $data
     * @param Registry $registry
     * @param ShippingModel $shippingModel
     */
    public function __construct(Template\Context $context, array $data = [], Registry $registry, ShippingModel $shippingModel)
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->registry = $registry;
        $this->shippingModel = $shippingModel;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->registry->registry('current_product');

        if ($product->getAttributeSetId() != $this->shippingModel->getConfigData('attribute_set')) {
            return '';
        }

        return parent::toHtml();
    }


}
