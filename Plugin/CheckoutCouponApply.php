<?php
/**
 * Ambab CustomCouponMsg Extension
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
 * @package     Ambab_CustomCouponMsg
 * @copyright   Copyright (c) 2019 Ambab (https://www.ambab.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Ambab\CustomCouponMsg\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Ambab\CustomCouponMsg\Helper\Validator as CouponValidator;
use Ambab\CustomCouponMsg\Helper\Data as ConfigData;

class CheckoutCouponApply
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $_couponValidator;
    public function __construct(
        CouponValidator $couponValidator,
        ConfigData $configData
    ) {
        $this->_couponValidator = $couponValidator;
    }

    public function beforeSet(\Magento\Quote\Model\CouponManagement $subject, $cartId, $couponCode)
    {
        if ($this->_configData->isEnabled()) {
            $msg= $this->_couponValidator->validate($couponCode);
            if ($msg != '') {
                throw new LocalizedException(__("%l", $msg));
            }
        }
    }
}
