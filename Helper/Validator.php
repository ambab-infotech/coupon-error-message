<?php
namespace Ambab\CustomCouponMsg\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\SalesRule\Model\CouponFactory;
use Ambab\CustomCouponMsg\Helper\Data as ConfigData ;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory;
use Magento\Framework\DataObjectFactory;
use Magento\SalesRule\Model\Rule\CustomerFactory;

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
        CustomerFactory $customerFactory
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
    }
    

    public function validate($couponCode)
    {
        $msg="";
        $coupon = $this->_couponFactory->create();
        $coupon->load($couponCode, 'code');
        // echo "usagelimit".$coupon->getUsageLimit();
        // echo 'timeused'.$coupon->getTimesUsed();
        // exit();
        //$couponCodeData = $this->_rule->create()->load($coupon->getruleId());
        //echo "<pre>";
        // print_r($couponCodeData->getData());
        //exit();
        

        /* check if coupon exit or not*/

        if (empty($coupon->getData())) {
            $msg=$this->_configData->isCouponExits();
        } else {

            // check for coupon expiry
            
            $couponExpiry = $this->checkExpiry($coupon->getexpirationDate());
            if ($couponExpiry) {
                $msg=$this->_configData->isCouponExpired();
            }

            //validation for customer group
            $couponCustomerGroup = $this->validateCustomerGroup($coupon->getruleId());
            if ($couponCustomerGroup) {
                $msg=$this->_configData->isCouponCustomerGroup();
            }

            // validation for website
            $couponWebsite = $this->validateCurrentWebsite($coupon->getruleId());
            if ($couponWebsite) {
                $msg=$this->_configData->isCouponWebsite();
            }

            //validate the number of usages
            $couponUsages = $this->validateCouponUsages($coupon);
            if ($couponUsages) {
                $msg=$this->_configData->isCouponUsage();
            }

            //validate cart condition
            $couponCondition=$this->validateCondition($coupon);
        }
        $msg=str_replace("%s", $couponCode, $msg);
        //echo $msg;
        //exit();
        //$msg = "called";
        return $msg;
    }

    /** check if coupon is expired or not
    *
    * @return bool
    *
    **/
    protected function checkExpiry($couponDate)
    {
        $now = $this->_date->date()->format('Y-m-d');
        if (strtotime($couponDate) < strtotime($now)) {
            return true;
        }
        return false;
    }

    /** check if coupon is assigned to current customer group
    * @param integer
    * @return bool
    *
    **/
    protected function validateCustomerGroup($ruleId)
    {
        $customerGroup=0;
        $couponCodeData = $this->_rule->create()->load($ruleId);

        if ($this->_customerSession->isLoggedIn()) {
            $customerGroup=$this->_customerSession->getCustomer()->getGroupId();
        }
        if (!in_array($customerGroup, $couponCodeData->getCustomerGroupIds())) {
            return true;
        }

        return false;
    }

    /** check if coupon is applicable for current website
    * @param integer
    * @return bool
    *
    **/
    protected function validateCurrentWebsite($ruleId)
    {
        $currentWebsite=$this->_storeManager->getStore()->getWebsiteId();
        $couponCodeData = $this->_rule->create()->load($ruleId);
        if (!in_array($currentWebsite, $couponCodeData->getWebsiteIds())) {
            return true;
        }

        return false;
    }

    /** check coupon usages
    * @param Object
    * @return bool
    *
    **/
    protected function validateCouponUsages($coupon)
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
        $rule= $this->_rule->create()->load($coupon->getruleId());
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
}
