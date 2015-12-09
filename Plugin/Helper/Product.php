<?php

namespace Meetup\Shipping\Plugin\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Meetup\Shipping\Model\Carrier\Shipping;

class Product
{
    /**
     * @var Shipping
     */
    private $shippingModel;

    public function __construct(Shipping $shippingModel)
    {
        $this->shippingModel = $shippingModel;
    }

    /**
     * @param $subject
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function afterInitProduct($subject, ProductInterface $product)
    {
        if ($this->shippingModel->getConfigData('attribute_set') != $product->getAttributeSetId()) {
            $product->setPrice($product->getPrice() * 0.9);
        }

        return $product;
    }
}
