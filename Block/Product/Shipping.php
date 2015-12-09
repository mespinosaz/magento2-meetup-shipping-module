<?php

namespace Meetup\Shipping\Block\Product;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Shipping extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Template\Context $context
     * @param array $data
     * @param Registry $registry
     */
    public function __construct(Template\Context $context, array $data = [], Registry $registry)
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->registry = $registry;
    }

    public function toHtml()
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */

        $product = $this->registry->registry('current_product');

        return parent::toHtml();
    }


}
