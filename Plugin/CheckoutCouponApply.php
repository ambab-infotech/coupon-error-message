<?php

namespace Ambab\CustomCouponMsg\Plugin;

class CheckoutCouponApply
{
    public function beforeSet(CouponManagement $subject, $cartId, $couponCode)
    {
        echo "called";
        exit();
    }
}
