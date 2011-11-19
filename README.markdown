Samurai
=======

If you are an online merchant and using samurai.feefighers.com, this PHP client
library will make your life easy. Integrate with the samurai.feefighters.com
portal and process transactions.


Installation
------------

Install Samurai by cloning this github repository:

    git clone git://github.com/FeeFighters/samurai-client-php.git

Alternatively, you can download the .tar.gz or .zip archive.


Configuration
-------------

Use the `Samurai::setup()` method to get the Samurai module ready for
action. You should pass an array, containing your merchant credentials
as a parameter. Here's an example:

    Samurai::setup(array(
      'merchantKey'      => 'your_merchant_key',
      'merchantPassword' => 'your_merchant_password',
      'processorToken'   => 'your_default_processor_token'
    ));

The `processorToken` param is optional. If you set it,
`Samurai_Processor::theProcessor()` will return the processor with this token. You
can always call `Samurai_Processor::find('an_arbitrary_processor_token')` to
retrieve any of your processors.

Samurai API Reference
---------------------

See the [API Reference](https://samurai.feefighters.com/developers/php/api-reference) for a full explanation of how this library works with the Samurai API.
