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
     * @param ProductInterface $result
     * @return ProductInterface
     */
    public function afterInitProduct($subject, ProductInterface $result)
    {
        if ($this->shippingModel->isAvailableForProduct($result)) {
            $result->setPrice($result->getPrice() * 0.9);
        }

        return $result;
    }
}
