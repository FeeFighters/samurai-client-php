<?php
require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Samurai_TransactionTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
  	$this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod();
	}

	public function testCaptureShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
    $transaction->capture();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCaptureShouldBeSuccessfulForFullAmount() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
	  $transaction->capture(100.0);
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCaptureShouldBeSuccessfulForPartialAmount() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
	  $transaction->capture(50.0);
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testCaptureFailuresShouldReturnProcessorTransactionInvalidWithDeclinedAuth() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.02);
	  $transaction->capture();
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'This transaction type is not allowed.', $transaction->errors['processor.transaction'][0]->description );
	}
	public function testCaptureFailuresShouldReturnProcessorTransactionDeclined() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
    $transaction->capture(100.02);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The card was declined.', $transaction->errors['processor.transaction'][0]->description );
	}

	public function testCaptureFailuresShouldReturnInputAmountInvalid() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
	  $transaction->capture(100.10);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['input.amount'][0]->description );
	}

	public function testReverseOnCaptureShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
    $transaction->reverse();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testReverseOnCaptureShouldBeSuccessfulForFullAmount() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->reverse(100.00);
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testReverseOnCaptureShouldBeSuccessfulForPartialAmount() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->reverse(50.00);
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testReverseOnAuthorizeShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
	  $transaction->reverse();
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testReverseFailuresShouldReturnInputAmountInvalid() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->reverse(100.10);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['input.amount'][0]->description );
	}

	public function testCreditOnCaptureShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->credit();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCreditOnCaptureShouldBeSuccessfulForFullAmount() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->credit(100.00);
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testCreditOnCaptureShouldBeSuccessfulForPartialAmount() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->credit(50.00);
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testCreditOnAuthorizeShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
	  $transaction->credit();
		$this->assertTrue( $transaction->isSuccess() );
	}

	public function testCreditFailuresShouldReturnInputAmountInvalid() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->credit(100.10);
		$this->assertTrue( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['input.amount'][0]->description );
	}

	public function testVoidOnAuthorizedShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 100.00);
	  $transaction->void();
		$this->assertTrue( $transaction->isSuccess() );
	}
	public function testVoidOnCapturedShouldBeSuccessful() {
	  $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 100.00);
	  $transaction->void();
		$this->assertTrue( $transaction->isSuccess() );
	}

}
