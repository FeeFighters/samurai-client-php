<?php

require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

function createTestPurchase($amount = 1.0, $overrides = array()) {
	$paymentMethod = Samurai_TestHelper::createTestPaymentMethod($overrides);
	$transaction = Samurai_Processor::theProcessor()->purchase(
		$paymentMethod->token,
		$amount,
		array('billing_reference' => rand(0, 1000))
	);
	return $transaction;
}

class Samurai_TransactionTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
  	$this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod();
	}

	public function testCaptureShouldBeSuccessful() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->capture();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCaptureShouldBeSuccessfulForFullAmount() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->capture(100.0);
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCaptureShouldBeSuccessfulForPartialAmount() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->capture(50.0);
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testCaptureFailuresShouldReturnProcessorTransactionInvalidWithDeclinedAuth() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.02);
	  $transaction = $auth->capture();
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'This transaction type is not allowed.', $transaction->errors['processor.transaction'][0]->description );
	}
	public function testCaptureFailuresShouldReturnProcessorTransactionDeclined() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->capture(100.02);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The card was declined.', $transaction->errors['processor.transaction'][0]->description );
	}
	public function testCaptureFailuresShouldReturnInputAmountInvalid() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->capture(100.10);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['processor.transaction'][0]->description );
	}

	public function testReverseOnCaptureShouldBeSuccessful() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->reverse();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testReverseOnCaptureShouldBeSuccessfulForFullAmount() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->reverse(100.00);
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testReverseOnCaptureShouldBeSuccessfulForPartialAmount() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->reverse(50.00);
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testReverseOnAuthorizeShouldBeSuccessful() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->reverse();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testReverseOnAuthorizefailuresShouldReturnInputAmountInvalid() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->reverse(100.10);
		$this->assertTrue( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['processor.transaction'][0]->description );
	}

	public function testCreditOnCaptureShouldBeSuccessful() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->credit();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCreditOnCaptureShouldBeSuccessfulForFullAmount() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->credit(100.00);
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCreditOnCaptureShouldBeSuccessfulForPartialAmount() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->credit(50.00);
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testCreditOnAuthorizeShouldBeSuccessful() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->credit();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCreditOnAuthorizefailuresShouldReturnInputAmountInvalid() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->credit(100.10);
		$this->assertTrue( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['processor.transaction'][0]->description );
	}

	public function testVoidOnAuthorizedShouldBeSuccessful() {
	  $auth = Samurai::Processor.authorize($this->paymentMethod->token, 100.00);
	  $transaction = $auth->void();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testVoidOnCapturedShouldBeSuccessful() {
	  $purchase = Samurai::Processor.purchase($this->paymentMethod->token, 100.00);
	  $transaction = $purchase->void();
		$this->assertTrue( $transaction->isSuccess() );
	}
}
