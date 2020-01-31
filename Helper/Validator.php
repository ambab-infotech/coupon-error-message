<?php
/**
 * Ambab CouponErrorMessage Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ambab
 * @package     Ambab_CouponErrorMessage
 * @copyright   Copyright (c) 2019 Ambab (https://www.ambab.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Ambab\CouponErrorMessage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\SalesRule\Model\CouponFactory;
use Ambab\CouponErrorMessage\Helper\Data as ConfigData ;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory;
use Magento\Framework\DataObjectFactory;
use Magento\SalesRule\Model\Rule\CustomerFactory;
use Magento\Quote\Model\Quote\Address;

class Validator extends AbstractHelper
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_date;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_rule;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory
     */
    protected $_usage;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Item\AbstractItem
     */
    protected $_abstractItem;

    /**
     * Array of conditions attached to the current rule.
     *
     * @var array
     */
    protected $_conditions = [];

    protected $_serialize;
     
    public function __construct(
        Context $context,
        CouponFactory $couponFactory,
        ConfigData $configData,
        EncryptorInterface $encryptor,
        TimezoneInterface $date,
        CustomerSession $customerSession,
        RuleFactory $rule,
        StoreManagerInterface $storeManager,
        UsageFactory $usage,
        DataObjectFactory $objectFactory,
        CustomerFactory $customerFactory,
        Address $address
    ) {
        parent::__construct($context);
        $this->_couponFactory = $couponFactory;
        $this->_configData = $configData;
        $this->_encryptor = $encryptor;
        $this->_date = $date;
        $this->_customerSession = $customerSession;
        $this->_rule = $rule;
        $this->_storeManager =$storeManager;
        $this->_usage = $usage;
        $this->_objectFactory = $objectFactory;
        $this->_customerFactory = $customerFactory;
        $this->_address = $address;
    }
    /**
     * Validate the coupon code
     *
     * @param str $couponCode
     * @return bool
     **/
    
    public function validate($couponCode)
    {
        $msg="";
        $coupon = $this->_couponFactory->create();
        $coupon->load($couponCode, 'code');
        
        /* check if coupon exit or not*/
        if (empty($coupon->getData())) {
            $msg = $this->_configData->isCouponExits();
            $msg = str_replace("%s", $couponCode, $msg);
            return $msg;
        } else {

            // check for coupon expiry
            $couponExpiry = $this->checkExpiry($coupon->getexpirationDate());
            if ($couponExpiry) {
                $msg = $this->_configData->isCouponExpired();
                $msg = str_replace("%s", $couponCode, $msg);
                return $msg;
            }

            //validation for customer group
            $couponCustomerGroup = $this->validateCustomerGroup($coupon->getruleId());
            if ($couponCustomerGroup) {
                $msg = $this->_configData->isCouponCustomerGroup();
                $msg = str_replace("%s", $couponCode, $msg);
                return $msg;
            }

            // validation for website
            $couponWebsite = $this->validateCurrentWebsite($coupon->getruleId());
            if ($couponWebsite) {
                $msg = $this->_configData->isCouponWebsite();
                $msg = str_replace("%s", $couponCode, $msg);
                return $msg;
            }

            //validate the number of usages
            $couponUsages = $this->validateCouponUsages($coupon);
            if ($couponUsages) {
                $msg = $this->_configData->isCouponUsage();
                $msg = str_replace("%s", $couponCode, $msg);
                return $msg;
            }

            //validate cart condition
            $couponCondition = $this->validateCondition($coupon);
            if ($couponCondition) {
                $msg=$this->_configData->isConditionFail();
                $msg=str_replace("%s", $couponCode, $msg);
                return $msg;
            }
        }
    }

    /**
     * Check if coupon is expired or not
     *
     * @param datetime $couponDate
     * @return bool
     **/
    protected function checkExpiry($couponDate)
    {
        $now = $this->_date->date()->format('Y-m-d');
        if (strtotime($couponDate) < strtotime($now)) {
            return true;
        }
        return false;
    }

    /**
     * Check if coupon is assigned to current customer group
     *
     * @param int $ruleId
     * @return bool
     *
     **/
    protected function validateCustomerGroup($ruleId)
    {
        $customerGroup = 0;
        $couponCodeData = $this->_rule->create()->load($ruleId);

        if ($this->_customerSession->isLoggedIn()) {
            $customerGroup = $this->_customerSession->getCustomer()->getGroupId();
        }
        if (!in_array($customerGroup, $couponCodeData->getCustomerGroupIds())) {
            return true;
        }

        return false;
    }

    /**
     * Check if coupon is applicable for current website
     *
     * @param ruleId
     * @return bool
     **/
    protected function validateCurrentWebsite($ruleId)
    {
        $currentWebsite = $this->_storeManager->getStore()->getWebsiteId();
        $couponCodeData = $this->_rule->create()->load($ruleId);
        if (!in_array($currentWebsite, $couponCodeData->getWebsiteIds())) {
            return true;
        }

        return false;
    }

    /**
     * Check coupon usages
     *
     * @param Magento\SalesRule\Model\CouponFactory
     * @return bool
     **/
    protected function validateCouponUsages(\Magento\SalesRule\Model\CouponFactory $coupon)
    {
        // check entire usage limit
        if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
            return true;
        }
        // check per customer usage limit
        $customerId = $this->_customerSession->getCustomer()->getId();
        if ($customerId && $coupon->getUsagePerCustomer()) {
            $couponUsage = $this->_objectFactory->create();
            $this->_usage->create()->loadByCustomerCoupon(
                $couponUsage,
                $customerId,
                $coupon->getId()
            );
            if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                return true;
            }
        }
        $rule = $this->_rule->create()->load($coupon->getruleId());
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
            $ruleCustomer = $this->_customerFactory->create();
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if coupon is validated condition
     *
     * @param Magento\SalesRule\Model\CouponFactory
     * @return bool
     **/
    protected function validateCondition(\Magento\SalesRule\Model\CouponFactory $coupon)
    {
        $rule = $this->_rule->create()->load($coupon->getruleId());
        $address = $this->_address;
        if (!$rule->validate($address)) {
            return true;
        }

        return false;
    }
}
