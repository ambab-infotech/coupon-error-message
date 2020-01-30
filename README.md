# Ambab Custom Coupon Error Message
A simple extension for magento 2.

## What Is Ambab Custom Coupon Error Message?

#Let Customers Know why their Coupon Code is Not Working

A faulty Magento coupon code that isn’t working can result in decreased sales and more work for a customer service team, who must explain the error to customers each time.

The Ambab Custom Coupon Code Error Messages solves that and more. The extension allows Magento store administrators to create, track, and display custom coupon error messages when customers attempt to apply a coupon code to their shopping cart, and for whatever reason, the discount cannot be applied.


#Installation

Install the extension through composer package manager.

composer require ambab/module-couponerrormessage
bin/magento module:enable Ambab_CouponErrorMessage

You can check if the module has been installed using bin/magento module:status

You should be able to see Ambab_CouponErrorMessage in the module list

Go to Admin -> Stores -> Configuration -> Ambab -> CouponErrorMessage to configure the error messages for coupon code.

Support
Visit www.ambab.com for support requests.