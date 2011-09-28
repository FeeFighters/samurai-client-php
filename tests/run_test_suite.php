#!/usr/bin/env phpunit
<?php

  require_once 'PHPUnit/Autoload.php';

  class SamuraiTestSuite extends PHPUnit_Framework_TestCase {

    static $payment_method_token;
    static $payment_method;
    static $transaction_reference_id;

    public static function setUpBeforeClass ( ) {
      require_once '../Samurai.php';
      require_once '../samurai_credentials.php';
      Samurai::$merchant_key = SAMURAI_MERCHANT_KEY;
      Samurai::$merchant_password = SAMURAI_MERCHANT_PASSWORD;
    }

    public function testSubmitCreditCard ( ) {

      $params = array();
      $params['credit_card[first_name]']    = 'John';
      $params['credit_card[last_name]']     = 'Smith';
      $params['credit_card[address_1]']     = '123 Main St';
      $params['credit_card[address_2]']     = 'Apt 1A';
      $params['credit_card[city]']          = 'Chicago';
      $params['credit_card[state]']         = 'IL';
      $params['credit_card[zip]']           = '60607';
      $params['credit_card[card_type]']     = 'visa';
      #$params['credit_card[card_number]']   = '41111111111111111';
      $params['credit_card[card_number]']   = '6011000000000012';
      $params['credit_card[cvv]']           = '123';
      $params['credit_card[expiry_month]']  = '09';
      $params['credit_card[expiry_year]']   = '19';
      $params['redirect_url']               = 'http://example.com/nowhere';
      $params['merchant_key']               = SAMURAI_MERCHANT_KEY;

      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_URL, 'https://api.samurai.feefighters.com/v1/payment_methods' );
      curl_setopt( $ch, CURLOPT_USERAGENT, "FeeFighter's Samurai PHP Client v".Samurai::VERSION." Test Suite" );
      curl_setopt( $ch, CURLOPT_HEADER, TRUE );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
      curl_setopt( $ch, CURLOPT_POST, TRUE );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($params) );
      $headers = curl_exec( $ch );

      preg_match( '#Location: .*\?payment_method_token=([a-f0-9]+)#', $headers, $match );
      self::$payment_method_token = $match[1];

    }

    public function testRetrievePaymentMethod ( ) {
    
      $payment_method = SamuraiPaymentMethod::fetchByToken( self::$payment_method_token );
      $this->assertInstanceOf( 'SamuraiPaymentMethod', $payment_method );
      $this->assertTrue( $payment_method->getIsSensitiveDataValid() );
      self::$payment_method = $payment_method;

    }

    public function testRetainPaymentMethod ( ) {

      self::$payment_method->retain();
      $this->assertTrue( self::$payment_method->getIsRetained() );
      $this->assertFalse( self::$payment_method->getIsRedacted() );

    }
  
    public function testRedactPaymentMethod ( ) {

      self::$payment_method->redact();
      $this->assertTrue( self::$payment_method->getIsRetained() );
      $this->assertTrue( self::$payment_method->getIsRedacted() );

    }
 
    public function testSubmitPurchase ( ) {

      $transaction = new SamuraiTransaction();
      $transaction->setAmount( 60.00 );
      $transaction->setCurrencyCode( 'USD' );
      $transaction->setPaymentMethodToken( self::$payment_method->getToken() );
      $transaction->setBillingReference( 'Billing Reference 1234' );
      $transaction->setCustomerReference( 'Customer #1' );

      $processor = new SamuraiProcessor( SAMURAI_PROCESSOR_TOKEN );
      $samurai_response = $transaction->purchase( $processor );

      self::$transaction_reference_id = $transaction->getReferenceId();
    }

    public function testVoidPurchase ( ) {
      $transaction = SamuraiTransaction::fetchByReferenceId( self::$transaction_reference_id );
      $samurai_response = $transaction->void();
      $processor_response = $samurai_response->getField( 'processor_response' );
      $this->assertTrue( $processor_response['success'] );
    }

  }

?>
