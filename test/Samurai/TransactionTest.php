<?php

require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

function createTestPurchase($amount=1.0) {
	$paymentMethod = Samurai_TestHelper::createTestPaymentMethod();
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
		$this->transaction = createTestPurchase();
	}

	public function testCreatePurchase() {
		$this->assertTrue($this->transaction->isSuccess());
	}

	public function testFindTransaction() {
		$transaction = Samurai_Transaction::find($this->transaction->referenceId);
		$this->assertEquals($this->transaction->referenceId, $transaction->referenceId);
		$this->assertEquals($this->transaction->token, $transaction->token);
	}

	public function testVoid() {
		$this->transaction->void();
		$this->assertContains($this->transaction->transactionType, array('Credit', 'Void'));
		$this->assertTrue($this->transaction->isSuccess());
	}

	public function testCreditWithFullAmount() {
		$this->transaction->credit();
		$this->assertContains($this->transaction->transactionType, array('Credit', 'Void'));
		$this->assertTrue($this->transaction->isSuccess());
	}

	public function testCreditWithPartialAmount() {
		$this->transaction->credit(0.5);
		$this->assertEquals(0.5, $this->transaction->amount);
		$this->assertContains($this->transaction->transactionType, array('Credit', 'Void', 'Refund'));
		$this->assertTrue($this->transaction->isSuccess());
	}

	public function testReverse() {
		$this->transaction->reverse();
		$this->assertContains($this->transaction->transactionType, array('Credit', 'Void'));
		$this->assertTrue($this->transaction->isSuccess());
	}

	public function testDeclinedPurchase() {
		$transaction = createTestPurchase(1.02);
		$this->assertFalse($transaction->isSuccess());
		$this->assertEquals('The card was declined.', $transaction->errors['processor.transaction'][0]->description);
	}

	public function testAvsCvvResultCodes() {
		$this->assertEquals('Y', $this->transaction->avsResultCode);
		$this->assertEquals('1', $this->transaction->cvvResultCode);
	}
}
