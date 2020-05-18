# AllSecure EXCHANGE Magento™ Plugin

Accept payments in your Magento store using AllSecure **EXCHANGE** Platform.

Current version: 1.0.1

See a fully functional WooCommerce <a href="http://demo.allsecpay.xyz/cart/exchange/mage" target="_new">demo store</a> with AllSecure **EXCHANGE** as a payment gateway.

## How to install extension on magento2

1. copy folder allsecureexchange to your magento2 /app/code/ directory.
2. go to your magento2 root directory.
3. run this command :
   - php bin/magento module:enable allsecureexchange_allsecureexchange
   - php bin/magento setup:upgrade
   - php bin/magento setup:static-content:deploy
4. clear cache - php bin/magento cache:clean
5. setting folders and files permission.

## How to reindex on magento2 :
1. go to your magento2 root directory.
2. run this command : php bin/magento indexer:reindex

## How to update extension on magento2 :
1. copy file from src directory to your magento2 root directory.
2. go to your magento2 root directory.
3. run this command :
   - sudo rm -rf var/generation/*
   - sudo rm -rf pub/static/*
   - php bin/magento setup:upgrade
   - php bin/magento setup:static-content:deploy
4. setting folders and files permission.
5. clear cache - php bin/magento cache:clean

## How to deploy static content language :
1. go to your magento2 root directory.
2. run this command :
   - sudo rm -rf pub/static/*
   - php bin/magento setup:static-content:deploy
   - php bin/magento --ansi setup:static-content:deploy {language code}
     for example germany language :
	   - php bin/magento --ansi setup:static-content:deploy de_DE
3. setting folders dan files permission.