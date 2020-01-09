<?php

namespace Ambab\CustomCouponMsg\Observer;

use Ambab\CustomCouponMsg\Helper\Validator;

class CollectTotal implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        Validator $validator
    ) {
        $this->validator = $validator;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $remove = $controller->getRequest()->getParam('remove');

        if (!$remove) {
            $couponCode = $controller->getRequest()->getParam('coupon_code');
            $couponData=$this->validator->validate($couponCode);
           
            //print_r($couponData->getData());
        }
        //exit();
        return $this;
    }
}
