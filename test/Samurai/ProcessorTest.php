<?php

require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Samurai_ProcessorTest extends PHPUnit_Framework_TestCase
{
	public function testDefaultProcessor() {
		$processor = Samurai_Processor::theProcessor();
		$this->assertInstanceOf('Samurai_Processor', $processor);
		$this->assertEquals(Samurai::$processorToken, $processor->processorToken);
	}

	public function testPurchase() {
		$paymentMethod = Samurai_TestHelper::createTestPaymentMethod();
		$transaction = Samurai_Processor::theProcessor()->purchase($paymentMethod->token, 1.0, array(
			'descriptor' => 'A test purchase',
			'custom' => 'optional custom data',
			'billing_reference' => 'ABC123',
			'customer_reference' => 'Customer (123)'
		));

		$this->assertTrue($transaction->isSuccess());
	}

	public function testAuthorization() {
		$paymentMethod = Samurai_TestHelper::createTestPaymentMethod();
		$transaction = Samurai_Processor::theProcessor()->authorize($paymentMethod->token, 1.0);

		$this->assertTrue($transaction->isSuccess());
	}

	public function testFailedPurchaseDueToValidationError() {
		$paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array('credit_card[card_number]' => ''));
		$transaction = Samurai_Processor::theProcessor()->purchase($paymentMethod->token, 1.0, array(
			'descriptor' => 'A test purchase',
			'custom' => 'optional custom data',
			'billing_reference' => 'ABC123',
			'customer_reference' => 'Customer (123)'
		));

		//print_r($transaction->errors);
		$this->assertFalse($transaction->isSuccess());
	}
}
