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

namespace Ambab\CouponErrorMessage\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Ambab\CouponErrorMessage\Helper\Validator as CouponValidator;
use Ambab\CouponErrorMessage\Helper\Data as ConfigData;

class CheckoutCouponApply
{
    /**
     * Module Helper
     *
     * @var \Ambab\CouponErrorMessage\Helper\Validator
     */

    protected $_couponValidator;
    /**
     * Module helper
     *
     * @var \Ambab\CouponErrorMessage\Helper\Data
     */
    protected $_configData;

    public function __construct(
        CouponValidator $couponValidator,
        ConfigData $configData
    ) {
        $this->_couponValidator = $couponValidator;
        $this->_configData =$configData;
    }

    /**
     * this function runs before set for coupon api
     */
    public function beforeSet(\Magento\Quote\Model\CouponManagement $subject, $cartId, $couponCode)
    {
        if ($this->_configData->isEnabled()) {
            $msg = $this->_couponValidator->validate($couponCode);
            if (!empty($msg)) {
                throw new LocalizedException(__("%l", $msg));
            }
        }
    }
}
