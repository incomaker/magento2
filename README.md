# Incomaker Plugin for Magento

Sample: https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/next-steps/sample-data/composer-packages.html

Integrates **Incomaker** XML feeds and tracking API into Magento2.

## Installation

Go to the directory where your **Magento** is installed and run following:

```
composer require incomaker/magento2
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

Now open web browser, go to **Stores / Configuration**, change scope to **Main Website** 
and finish plugin settings under section **Incomaker**. 

Read more about plugin installation in the [Installation Instructions](https://support.incomaker.com/en/hc/2628921009/5/magento?category_id=4)

## Plugin Development in Docker

Information below is intended for plugin developers.

### Installation

Create configuration:

    cp .env-example .env

You can configure your own values inside `.env`, then run:

    docker compose up -d

Now you can visit:

    http://localhost/admin


Username and password are those you specified in `.env` file.
Not secure! For development only!

#### Read More about Magento in Docker...

https://hub.docker.com/r/bitnami/magento/

### Module Development

Online sources about Magento module development:

- https://developer.adobe.com/commerce/php/development/build/development-environment/
- https://www.mageplaza.com/devdocs/magento-2-module-development/
- https://meetanshi.com/blog/magento-2-module-development/

#### MGT - Dev Env

https://www.mgt-commerce.com/magento-2-local-development-environment

Run:

    ./mgt-dev

now go to `https://localhost:8443/`.

#### Install and Update Using Scripts

Install the plugin (needed only once):

    ./plugin-install

You will need access key managed at Adobe `https://commercemarketplace.adobe.com/customer/accessKeys/`.
Use account registered for `salamon@incomaker.com`. Use the Public key as your username and the Private key as your password.

Update the plugin (needed after plugin installation and after each code change):

    ./plugin-update

#### Install Plugin Manually

Log into the container:

    docker exec -ti magento-docker-magento2 /bin/bash

Go to Magento folder:

    cd /bitnami/magento

Get Incomaker plugin:

You will need access key managed at Adobe `https://commercemarketplace.adobe.com/customer/accessKeys/`.
Use account registered for `salamon@incomaker.com`. Use the Public key as your username and the Private key as your password.

```
composer require incomaker/magento2
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
bin/magento cache:flush
```

You might need to restart Docker container after this.
