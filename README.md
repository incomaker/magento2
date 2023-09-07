# Incomaker Plugin for Magento

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

### Plugin Development

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
