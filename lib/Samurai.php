<?php

abstract class Samurai 
{
  const VERSION = '0.1.4';

  public static $merchantKey;
  public static $merchantPassword;
  public static $processorToken;
  public static $site = 'https://api.samurai.feefighters.com/v1/';
  public static $sandbox = false;
  public static $debug = false;  

  /*
   * Sets up the default connection parameters for all requests to the Samurai API.
   * Parameters are passed in a single object. The available parameters are:
   *
   *   * `merchantKey`: Your merchant key. Required.
   *   * `merchantPassword`: Your merchant password. Required.
   *   * `processorToken`: Your default processor token. Optional.
   *   * `site`: Root URL to Samurai's API. Default: https://api.samurai.feefighters.com/v1/
   *   * `sandbox`: Tells samurai to include the sandbox=true parameter with payment method requests, 
   *     so you can execute tests with these payment methods on the sandbox processor. Default: false
   *
   */
  public static function setup($options) {
    $keys = array('merchantKey', 
                  'merchantPassword', 
                  'processorToken', 
                  'site', 
                  'sandbox',
                  'debug');

    foreach ($options as $option => $value) {
      if (in_array($option, $keys)) {
        self::$$option = $value;
      }
    }
  }
}

require_once dirname(__FILE__) . '/Samurai/Helpers.php';
require_once dirname(__FILE__) . '/Samurai/Errors.php';
require_once dirname(__FILE__) . '/Samurai/XmlParser.php';
require_once dirname(__FILE__) . '/Samurai/Message.php';
require_once dirname(__FILE__) . '/Samurai/Connection.php';
require_once dirname(__FILE__) . '/Samurai/Transaction.php';
require_once dirname(__FILE__) . '/Samurai/PaymentMethod.php';
require_once dirname(__FILE__) . '/Samurai/Processor.php';
require_once dirname(__FILE__) . '/Samurai/Views.php';

if (version_compare(PHP_VERSION, '5.2.1', '<')) {
  throw new Exception('PHP version >= 5.2.1 required');
}


function checkDependencies() {
  $extensions = array('curl', 'SimpleXML', 'openssl', 'curl');
  foreach ($extensions AS $ext) {
    if (!extension_loaded($ext)) {
      throw new Exception('samurai-client-php requires the ' . $ext . ' extension.');
    }
  }
}

checkDependencies();