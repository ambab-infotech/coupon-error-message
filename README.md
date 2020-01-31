
## Ambab StoreOrderPrefix

#Let Customers Know why their Coupon Code is Not Working

A faulty Magento coupon code that isn’t working can result in decreased sales and more work for a customer service team, who must explain the error to customers each time.

The Ambab Custom Coupon Code Error Messages solves that and more. The extension allows Magento store administrators to create, track, and display custom coupon error messages when customers attempt to apply a coupon code to their shopping cart, and for whatever reason, the discount cannot be applied.

## Features

- Admin can add custom error messages of coupon validation.

- 100% open source.

- Easy to install.


## Installation/ Uninstallation [Versions supported: 2.3.x onwards]

**Steps to install with composer**

- composer require ambab/module-couponerrormessage

- bin/magento module:enable Ambab_CouponErrorMessage

- bin/magento setup:upgrade

- bin/magento setup:di:compile

- bin/magento cache:flush

**Steps to uninstall a composer installed module**

- bin/magento module:disable Ambab_CouponErrorMessage

- bin/magento module:uninstall Ambab_CouponErrorMessage

- composer remove ambab/module-couponerrormessage

- bin/magento cache:flush


**Steps to install module manually in app/code**

- Add directory to app/code/Ambab/CouponErrorMessage/ manually

- bin/magento module:enable Ambab_CouponErrorMessage

- bin/magento setup:upgrade

- bin/magento cache:flush

**Steps to uninstall a manually added module in app/code**

- bin/magento module:disable Ambab_CouponErrorMessage

- remove directory app/code/Ambab/CouponErrorMessage manually

- bin/magento setup:upgrade

- bin/magento cache:flush


## Configurations

Go to Admin -> Stores -> Configuration -> **Select store view** -> Ambab -> Custom Coupon Error Message


## Contribute

Feel free to fork and contribute to this module by creating a PR to master branch (https://github.com/ambab-infotech/couponerromessage).

## Support

For issues please raise here https://github.com/ambab-infotech/couponerromessage/issues

In case of additional support feel free to reach out at tech.support@ambab.com
