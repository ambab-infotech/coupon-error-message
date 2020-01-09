<?php

namespace Ambab\CustomCouponMsg\Model;

class Utility extends \Magento\SalesRule\Model\Utility
{
    /**
     * @var ConfigSetting;
     */
    protected $configSetting;

    /**
     * @var CheckoutSession;
     */
    protected $checkoutSession;

    /**
     * @param \Ambab\CustomCouponMsg\Helper\Data $configSetting
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Ambab\CustomCouponMsg\Helper\Data $configSetting,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->configSetting = $configSetting;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     *  Overridden function for error message
     */
    public function canProcessRule($rule, $address)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/coupon.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('called overriden file');

        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        /**
         * check per coupon usage limit
         */
        if ($rule->getCouponType() != \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON) {
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                /** @var \Magento\SalesRule\Model\Coupon $coupon */
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                if ($coupon->getId()) {
                    // check entire usage limit
                    if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                        $rule->setIsValidForAddress($address, false);
                        $logger->info('called1');
                        $this->checkoutSession->setCouponMessage('override called');
                        return false;
                    }
                    // check per customer usage limit
                    $customerId = $address->getQuote()->getCustomerId();
                    if ($customerId && $coupon->getUsagePerCustomer()) {
                        $couponUsage = $this->objectFactory->create();
                        $this->usageFactory->create()->loadByCustomerCoupon(
                            $couponUsage,
                            $customerId,
                            $coupon->getId()
                        );
                        if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            $rule->setIsValidForAddress($address, false);
                            $logger->info('called2');
                           
                            $this->checkoutSession->setCouponMessage('override called');
                            return false;
                        }
                    }
                }
            }
        }

        /**
         * check per rule usage limit
         */
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            $customerId = $address->getQuote()->getCustomerId();
            /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
            $ruleCustomer = $this->customerFactory->create();
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    $rule->setIsValidForAddress($address, false);
                    $logger->info('called3');
                    $this->checkoutSession->setCouponMessage('override called');
                    return false;
                }
            }
        }
        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            $logger->info('called4');
            $this->checkoutSession->setCouponMessage('override called');
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForAddress($address, true);
        return true;
    }
}
