# Incomaker Module for Magento2

## Incomaker Customers

This module for **Magento2** integrates Incomaker's XML feeds and tracking API into your e-shop.

### Installation

Go to the directory where your **Magento** is installed and run following:

```
composer require incomaker/magento2
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
bin/magento cache:flush
```

Now open web browser, go to admin area of your e-shop, select **Stores / Configuration**, change scope to **Main Website**
and finish module settings under section **Incomaker**.

Read more about plugin installation in the [Installation Instructions](https://support.incomaker.com/en/hc/2628921009/5/magento?category_id=4)

When module is successfully installed and configured, you will be able to access XML feeds:

    https://<your-domain>/incomaker/data/feed?key=<your-api-key>&type=product

## Module Developers

*NOTE: Information below is intended for developers of this Magento module.*

Online sources about Magento2 module development:

- https://developer.adobe.com/commerce/php/development/build/development-environment/
- https://www.mageplaza.com/devdocs/magento-2-module-development/
- https://meetanshi.com/blog/magento-2-module-development/

### Update Dependencies Locally

This will create `vendor` folder with all dependencies which is useful for code inspection inside IDE.

    bin/composer-install

### Set Up MGT - Development Environment

Read about **MGT-DEV**: https://www.mgt-commerce.com/magento-2-local-development-environment

#### Access Keys

You will need access keys from Adobe: `https://commercemarketplace.adobe.com/customer/accessKeys/`.
Use account registered for `salamon@incomaker.com`. Use the Public key as your username and the Private key as your password.

#### Start MGT Environment

Run:

    bin/mgt-dev

then go to UI: `https://localhost:8443/`

- add domain (e.g. `incomaker.mgt`, Work dir: `incomaker.mgt/pub` - must end with `pub`)
- edit `hosts` file and add the same domain
- add database (e.g. `incomaker`)
- add cron (e.g. `cd /home/cloudpanel/htdocs/incomaker.mgt && bin/magento cron:run`)

Now use convenience script to create Magento project

    bin/mgt-install <instance_name, e.g. incomaker>

or do it the hard way:

#### SSH Into the MGT Environment

All further commands must be issued via SSH:

    ssh root@127.0.0.1

#### Install Magento

Create new project:

    cd /home/cloudpanel/htdocs/
    rm incomaker.mgt -rf
    composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition incomaker.mgt

Install Magento:

    cd incomaker.mgt
    bin/magento setup:install \
        --backend-frontname='admin' --key='18Av6ITivOZG3gwY1DhMDWtlLfx1spLP' \
        --session-save='files' --db-host='127.0.0.1' --db-name='incomaker' \
        --db-user='incomaker' --db-password='incomaker' \
        --base-url='http://incomaker.mgt/' --base-url-secure='https://incomaker.mgt/' \
        --admin-user='admin' --admin-password='!admin123!' \
        --admin-email='john@doe.com' --admin-firstname='John' --admin-lastname='Doe'
    chmod -R 777 /home/cloudpanel/htdocs/incomaker.mgt

Disable Two-Factor:

    bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth

Install Incomaker Module:

    composer require incomaker/magento2
    bin/magento setup:upgrade
    bin/magento setup:di:compile
    bin/magento cache:clean
    bin/magento cache:flush
    chmod -R 777 /home/cloudpanel/htdocs/incomaker.mgt

Activate Developer Mode:

    bin/magento deploy:mode:set developer

Convenience script (does all of the above):

    bin/mgt-install

#### Sync files

You will have to configure file sync in your IDE between root folder of the project and
`root:root@127.0.0.1:/home/cloudpanel/htdocs/incomaker.mgt/vendor/incomaker/magento2`

After files are updated, you have to rebuild Magento DI:

    bin/magento setup:di:compile
    bin/magento cache:clean
    bin/magento cache:flush
    chmod -R 777 /home/cloudpanel/htdocs/incomaker.mgt

or simply `bin/mgt-update` from host or `mgt-update` from inside the container.

#### View Logs

    watch tail var/log/debug.log

### Build and Deploy

#### Deploy to Packagist

- create and checkout new branch (e.g. `v1.1`)
- increase version number inside `composer.json` (e.g. `1.1.4`)
- commit
- create new version tag (e.g. `1.1.4`)
- push to **GitHub**

#### Package as ZIP

    bin/module-package
