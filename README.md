PHP SDK for NullNude API
========================

[![Build Status](https://img.shields.io/travis/dneural/php-nullnude/master.svg)](https://travis-ci.org/dneural/php-nullnude)
[![Latest Stable Version](http://img.shields.io/badge/Latest%20Stable-1.0.0-blue.svg)](https://packagist.org/packages/dneural/php-nullnude)

This PHP SDK is a wrapper for our [NullNude API](https://nullnude.com), 
a nudity detection/moderation service. 

Use the NullNude API to instantly moderate adult content in user-submitted photos.


Installation
------------

The NullNude PHP SDK can be installed with [Composer](https://getcomposer.org/). 

    php composer.phar require dneural/php-nullnude dev-master

or by adding the repository to your **composer.json** by hand:

```json
{
    "require": {
        "dneural/php-nullnude": "dev-master"
    }
}

```

and then installing the SDK by running:

    php composer.phar install

Check the `examples` directory to learn how to use the SDK effectivly.


Authenticate to NullNude API
----------------------------

Each **application** that uses NullNude API needs to be authenticated. For that 
reason you will have to register an account with us. It is a very easy process 
and can be done at this address: https://nullnude.com/register

Write down your api_key and api_secret and you're ready to go.


Checking for nudity, regions of interest.
-----------------------------------------

Checking if images contain nudity in them is easy. Provide an URL or a local 
file path of the image you would like checked to one of the few methods that
our API supports.

```php
<?php
require __DIR__ . '/vendor/autoload.php';
use NullNude\NullNude;

// Information about your API access.
$config = [
    'api_key'    => 'YOUR_API_KEY',
    'api_secret' => 'YOUR_API_SECRET'
];

// Initialize the NullNude client.
$nullNude = new NullNude($config);

// Image can be either a local path or an external url.
$image = "https://nullnude.com/wp-content/uploads/2016/01/vintage_porn_2.jpg";

// Check if the image has nuidty in it.
$nudityResource = $nullNude->checkNudity($image);
if ( $nudityResource->hasNudity() ) {
    // Take action based on your confidence preference.
    echo 'Image nudity confidence: ' . 
         $nudityResource->getNudityConfidence();
}

// Get the array of regions of interest within the image.
$roiResource = $nullNude->getRoi($image);
echo "<pre>";
print_r($roiResource->getRoi());
echo "</pre>";

// Check if the image has been moderated, moderate.json 
// applies the filter only if there was nudity present.
$moderateResource = $nullNude->moderate($image);
if ( $moderateResource->isModerated() ) {
    // Download and save the moderated image.
    echo 'Moderated image url: ' . 
         $moderateResource->getModeratedUrl();
}

```

Consider checking the `examples` directory for more real life usage examples.

How to build the documentation?
-------------------------------

Documentation is based on phpdocumentor. To install it clone the php-nullnude
project:

    git clone https://github.com/dneural/php-nullnude.git
    cd php-nullnude
    php composer.phar install

To generate documentation in the ./docs/nullnude directory run:

    ./vendor/bin/phpdoc -d ./src -t ./docs/nullnude


How to run tests?
-----------------

Tests are based on phpunit. To run them clone the php-nullnude
project:

    git clone https://github.com/dneural/php-nullnude.git
    cd php-nullnude
    php composer.phar install

and run this command:

    ./vendor/bin/phpunit --coverage-text


License
-------

The SDK code is released under a MIT style license, which means that it should 
be easy to integrate it in your applications.  
Check the [LICENSE](LICENSE) file for more information.