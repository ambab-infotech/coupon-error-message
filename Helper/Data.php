<?php
namespace Ambab\CustomCouponMsg\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
    /** * @var EncryptorInterface */
    protected $encryptor;
    /** * @param Context $context * @param EncryptorInterface $encryptor */

    public function __construct(Context $context, EncryptorInterface $encryptor)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /* * @return bool */
    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag('customcouponmsg/general/enable', $scope);
    }

        
    /* * @return bool */

    public function isCouponExits($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('customcouponmsg/general/coupon_exist', $scope);
    }

    /* * @return bool */

    public function isConditionFail($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('customcouponmsg/general/condition_fail', $scope);
    }

    /* * @return bool */

    public function isCouponExpired($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('customcouponmsg/general/coupon_expired', $scope);
    }

    /* * @return bool */

    public function isCouponCustomerGroup($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('customcouponmsg/general/coupon_customer_group', $scope);
    }

    /* * @return bool */

    public function isCouponWebsite($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('customcouponmsg/general/coupon_website_id', $scope);
    }

    /* * @return bool */

    public function isCouponUsage($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('customcouponmsg/general/coupon_usages', $scope);
    }
}
