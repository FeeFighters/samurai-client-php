Samurai
=======

If you are an online merchant and using samurai.feefighers.com, this PHP client
library will make your life easy. Integrate with the samuari.feefighters.com
portal and process transactions.


Installation
------------

Install Samurai by cloning this github repository

    git clone git://github.com/FeeFighters/samurai-client-php.git

Otherwise, download the .tar.gz or .zip archive

Configuration
-------------

You need to tell the client library what your Samurai keys are by defining constants:

    <?
      define( 'SAMURAI_MERCHANT_KEY', 'CHANGE TO YOUR MERCHANT KEY' );
      define( 'SAMURAI_MERCHANT_PASSWORD', 'CHANGE TO YOUR MERCHANT PASSWORD' );
      define( 'SAMURAI_PROCESSOR_TOKEN', 'CHANGE TO YOUR PROCESSOR TOKEN' );
      define( 'SAMURAI_LIB_DIRECTORY', dirname(__DIR__).'/samurai-client-php' );
    ?>

Samurai API Reference
---------------------

See the [API Reference](https://samurai.feefighters.com/developers/api-reference/php) for a full explanation of how this library works with the Samurai API.
