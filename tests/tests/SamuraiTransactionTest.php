<?php
  require_once 'PHPUnit/Autoload.php';

  class SamuraiTransactionTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass ( ) {
      require_once '../Samurai.php';
      require_once 'test_samurai_credentials.php';
      require_once 'test_utilities.php';
      Samurai::$merchant_key = SAMURAI_MERCHANT_KEY;
      Samurai::$merchant_password = SAMURAI_MERCHANT_PASSWORD;
    }

    public function testSubmitPurchase ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $transaction = new SamuraiTransaction();
      $transaction->setAmount( 60.00 );
      $transaction->setCurrencyCode( 'USD' );
      $transaction->setPaymentMethodToken( $payment_method->getToken() );
      $transaction->setBillingReference( 'Billing Reference 1234' );
      $transaction->setCustomerReference( 'Customer #1' );

      $processor = new SamuraiProcessor( SAMURAI_PROCESSOR_TOKEN );
      $new_transaction = $transaction->purchase( $processor );
      $processor_response = $new_transaction->getProcessorResponse();
      $this->assertTrue( $processor_response->getSuccess() );
    }

    public function testDeclinedPurchase ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $transaction = new SamuraiTransaction();
      $transaction->setAmount( 60.02 );
      $transaction->setCurrencyCode( 'USD' );
      $transaction->setPaymentMethodToken( $payment_method->getToken() );

      $processor = new SamuraiProcessor( SAMURAI_PROCESSOR_TOKEN );
      $new_transaction = $transaction->purchase( $processor );
      $processor_response = $new_transaction->getProcessorResponse();

      $this->assertFalse( $processor_response->getSuccess() );
      $messages = $processor_response->getMessages();
      $error = $messages[0];
      $this->assertEquals( $error->getClass(), 'error' );
      $this->assertEquals( $error->getContext(), 'processor.transaction' );
      $this->assertEquals( $error->getKey(), 'declined' );
    }

    public function testInvalidCardPurchase ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $transaction = new SamuraiTransaction();
      $transaction->setAmount( 60.07 );
      $transaction->setCurrencyCode( 'USD' );
      $transaction->setPaymentMethodToken( $payment_method->getToken() );
      $processor = new SamuraiProcessor( SAMURAI_PROCESSOR_TOKEN );
      $new_transaction = $transaction->purchase( $processor );
      $processor_response = $new_transaction->getProcessorResponse();

      $this->assertFalse( $processor_response->getSuccess() );
      $messages = $processor_response->getMessages();
      $error = $messages[0];
      $this->assertEquals( $error->getClass(), 'error' );
      $this->assertEquals( $error->getContext(), 'input.card_number' );
      $this->assertEquals( $error->getKey(), 'invalid' );

    }

    public function testExpiredCardPurchase ( ) {
      $payment_method = SamuraiTestSuite::createPaymentMethod();
      $transaction = new SamuraiTransaction();
      $transaction->setAmount( 60.08 );
      $transaction->setCurrencyCode( 'USD' );
      $transaction->setPaymentMethodToken( $payment_method->getToken() );
      $processor = new SamuraiProcessor( SAMURAI_PROCESSOR_TOKEN );
      $new_transaction = $transaction->purchase( $processor );
      $processor_response = $new_transaction->getProcessorResponse();

      $this->assertFalse( $processor_response->getSuccess() );
      $messages = $processor_response->getMessages();
      $error = $messages[0];
      $this->assertEquals( $error->getClass(), 'error' );
      $this->assertEquals( $error->getContext(), 'input.expiry_month' );
      $this->assertEquals( $error->getKey(), 'invalid' );
    }

    public function testVoidPurchase ( ) {
      $transaction = SamuraiTestSuite::createTransaction();
      $new_transaction = $transaction->void();
      $processor_response = $new_transaction->getProcessorResponse();
      $this->assertTrue( $processor_response->getSuccess() );
    }

    public function testCreditPurchase ( ) {
      $transaction = SamuraiTestSuite::createTransaction();
      $new_transaction = $transaction->credit(0.5);
      $processor_response = $new_transaction->getProcessorResponse();
      $this->assertTrue( $processor_response->getSuccess() );
    }

    public function testReversePurchase ( ) {
      $transaction = SamuraiTestSuite::createTransaction();
      $new_transaction = $transaction->reverse();
      $processor_response = $new_transaction->getProcessorResponse();
      $this->assertTrue( $processor_response->getSuccess() );
    }


  }

?>
