## Coupon Error Message

### Let customers know why their coupon code is not working

Coupons help websites increase sales. When a customer makes a purchase on the website and applies a coupon code to get a discount, it will display the default Magento error message "Coupon code is not valid" if the coupon does not fulfill the conditions. The default message is not self-explanatory which explains the reason for coupon failure. Coupon Error Message extension for Magento 2 allows an admin to specify an error message for each condition of the coupon shopping cart rules. When the condition is not passed through validation, an appropriate error message will be shown to a customer which will be configured in the backend. 

For example, a customer has a coupon code that will be applicable that should apply a 40% discount for orders that total $100 or more. So the customer adds products with the total amount of $80 to the shopping cart and tries to apply the 40% coupon code and, of course, he gets the error message "Coupon code is not valid". With the help of the Coupon Error Message module, the customer will receive the error message that is set by admin like "Cart does not fulfill the condition for coupon".

 

## Feature Summary
### Admin Can Set:
- Enable/ Disable flag.  
- Error message when coupon does not exist.  
- Error message when coupon exists but is expired.  
- Error message when a customer doesn't belong to the assigned customer group.  
- Error message when the coupon is not applicable to the website.
- Error message when the coupon was used more than it can be used.  
- Error messages when coupon exists but do not apply to the cart rule conditions.  
 

### Benefits:
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

Go to Admin -> Stores -> Configuration -> Ambab -> Coupon Error Message


## Contribute

Feel free to fork and contribute to this module by creating a PR to master branch (https://github.com/ambab-infotech/coupon-error-message).

## Support

For issues please raise here https://github.com/ambab-infotech/coupon-error-message/issues

In case of additional support feel free to reach out at tech.support@ambab.com
