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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
    const ERRORMESSAGE_ENABLED    = 'couponerrormessage/general/enable';
    const COUPON_EXIST    = 'couponerrormessage/general/coupon_exist';
    const CONDTION_FAILED    = 'couponerrormessage/general/condition_fail';
    const COUPON_EXPIRED    = 'couponerrormessage/general/coupon_expired';
    const COUPON_CUSTOMER_GROUP    = 'couponerrormessage/general/coupon_customer_group';
    const COUPON_WEBSITE_ID    = 'couponerrormessage/general/coupon_website_id';
    const COUPON_USAGES    = 'couponerrormessage/general/coupon_usages';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */

    public function __construct(Context $context, EncryptorInterface $encryptor)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /**
     * @return bool
     */
    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag(self::ERRORMESSAGE_ENABLED, $scope);
    }

    /**
     * @return string
     */
    public function isCouponExits($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_EXIST, $scope);
    }

    /**
     * @return string
     */
    public function isConditionFail($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::CONDTION_FAILED, $scope);
    }

    /**
     * @return string
     */
    public function isCouponExpired($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_EXPIRED, $scope);
    }

    /**
     * @return string
     */
    public function isCouponCustomerGroup($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_CUSTOMER_GROUP, $scope);
    }

    /**
     * @return string
     */
    public function isCouponWebsite($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_WEBSITE_ID, $scope);
    }

    /**
     * @return string
     */
    public function isCouponUsage($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_USAGES, $scope);
    }
}
