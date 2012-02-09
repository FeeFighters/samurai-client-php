<?php

require_once 'PHPUnit/Autoload.php';

set_include_path(
  get_include_path() . PATH_SEPARATOR .
  realpath(dirname(__FILE__)) . '/../lib'
);

require_once 'Samurai.php';

Samurai::setup(array(
  'site'             => isset($_ENV['SITE'])              ? $_ENV['SITE']              : 'https://api.samurai.feefighters.com/v1/',
  'merchantKey'      => isset($_ENV['MERCHANT_KEY'])      ? $_ENV['MERCHANT_KEY']      : 'a1ebafb6da5238fb8a3ac9f6',
  'merchantPassword' => isset($_ENV['MERCHANT_PASSWORD']) ? $_ENV['MERCHANT_PASSWORD'] : 'ae1aa640f6b735c4730fbb56',
  'processorToken'   => isset($_ENV['PROCESSOR_TOKEN'])   ? $_ENV['PROCESSOR_TOKEN']   : '5a0e1ca1e5a11a2997bbf912',
  'debug'            => true
));

class Samurai_TestHelper
{
  public static function createTestPaymentMethod($overrides = array()) {
    $params = array(
      'credit_card[first_name]'    => 'FirstName',
      'credit_card[last_name]'     => 'LastName',
      'credit_card[address_1]'     => '1000 1st Av',
      'credit_card[address_2]'     => '',
      'credit_card[city]'          => 'Chicago',
      'credit_card[state]'         => 'IL',
      'credit_card[zip]'           => '10101',
      'credit_card[card_number]'   => '4111111111111111',
      'credit_card[cvv]'           => '111',
      'credit_card[expiry_month]'  => '05',
      'credit_card[expiry_year]'   => '2014',
      'redirect_url'               => 'http://yourdomain.com/anywhere',
      'merchant_key'               => Samurai::$merchantKey
    );

		if(!empty($overrides) && is_array($overrides)) {
			$params = array_merge($params, $overrides);
		}

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            'https://api.samurai.feefighters.com/v1/payment_methods');
    curl_setopt($ch, CURLOPT_USERAGENT,      'FeeFighters Samurai PHP Client v' . Samurai::VERSION . ' Test Suite');
    curl_setopt($ch, CURLOPT_HEADER,         1);
    curl_setopt($ch, CURLOPT_NOBODY,         1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POST,           1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($params));
    $headers = curl_exec($ch);

    $match = null;
    preg_match('/Location: .*\?payment_method_token=([a-f0-9]+)/', $headers, $match);
    return Samurai_PaymentMethod::find($match[1]); 
  }
}
