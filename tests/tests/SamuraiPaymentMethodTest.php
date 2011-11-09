<?php
  require_once 'PHPUnit/Autoload.php';

  class SamuraiPaymentMethodTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass ( ) {
      require_once '../Samurai.php';
      require_once 'test_samurai_credentials.php';
      require_once 'test_utilities.php';
      Samurai::$merchant_key = SAMURAI_MERCHANT_KEY;
      Samurai::$merchant_password = SAMURAI_MERCHANT_PASSWORD;
    }

    public function testSubmitCreditCard ( ) {
      SamuraiTestSuite::createPaymentMethod();
    }

    public function testRetrievePaymentMethod ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $payment_method = SamuraiPaymentMethod::fetchByToken( $payment_method->getToken() );
      $this->assertInstanceOf( 'SamuraiPaymentMethod', $payment_method );
      $this->assertTrue( $payment_method->getIsSensitiveDataValid() );
    }

    public function testRetainPaymentMethod ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $payment_method->retain();
      $this->assertTrue(  $payment_method->getIsRetained() );
      $this->assertFalse( $payment_method->getIsRedacted() );
    }

    public function testRedactPaymentMethod ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $payment_method->redact();
      $this->assertFalse( $payment_method->getIsRetained() );
      $this->assertTrue( $payment_method->getIsRedacted() );
    }
  }
?>
