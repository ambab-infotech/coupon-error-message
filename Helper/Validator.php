<?php
namespace Ambab\CustomCouponMsg\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\SalesRule\Model\CouponFactory;
use Ambab\CustomCouponMsg\Helper\Data;

class Validator extends AbstractHelper
{
    /** * @var EncryptorInterface */
    protected $encryptor;
    /** * @param Context $context * @param EncryptorInterface $encryptor */

    public function __construct(
        Context $context,
        CouponFactory $couponFactory,
        CartRepositoryInterface $quoteRepository,
        Data $configData
    ) {
        parent::__construct($context);
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
    }

    public function validate($couponCode)
    {
        $coupon = $this->couponFactory->create();
        $coupon->load($couponCode, 'code');
        if (empty($coupon->getData())) {
            $msg="Coupon doesn't exit";
        }
        //$msg = "called";
        return $msg;
    }
}
