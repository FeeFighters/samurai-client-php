<?php
require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Samurai_ProcessorTest extends PHPUnit_Framework_TestCase
{
  public function setUp() {
    $this->transactionAttribs = array(
  		'description' => 'description',
  		'descriptor_name' => 'descriptor_name',
  		'descriptor_phone' => 'descriptor_phone',
  		'custom' => 'custom_data',
  		'billing_reference' => 'ABC123'.rand(0, 1000),
  		'customer_reference' => 'Customer (123)',
  		'currency_code' => 'USD'
  	);
  	$this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod();
  }

	public function testTheProcessorShouldReturnTheDefaultProcessor() {
		$processor = Samurai_Processor::theProcessor();
		$this->assertInstanceOf('Samurai_Processor', $processor);
		$this->assertEquals(Samurai::$processorToken, $processor->processorToken);
	}

	public function testNewProcessorShouldReturnAProcessor() {
	  $processor = Samurai_Processor::find(Samurai::$processorToken);
		$this->assertInstanceOf('Samurai_Processor', $processor);
		$this->assertEquals(Samurai::$processorToken, $processor->processorToken);
	}

	public function testPurchaseShouldBeSuccessful() {
		$transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.0, $this->transactionAttribs);

		$this->assertTrue( $transaction->isSuccess() );
	  $this->assertEquals( $this->transactionAttribs['description'], $transaction->description );
	  $this->assertEquals( $this->transactionAttribs['descriptor_name'], $transaction->descriptor_name );
	  $this->assertEquals( $this->transactionAttribs['descriptor_phone'], $transaction->descriptor_phone );
	  $this->assertEquals( $this->transactionAttribs['custom'], $transaction->custom );
	  $this->assertEquals( $this->transactionAttribs['billing_reference'], $transaction->billing_reference );
	  $this->assertEquals( $this->transactionAttribs['customer_reference'], $transaction->customer_reference );
	  $this->assertEquals( $this->transactionAttribs['currency_code'], $transaction->currency_code );
	}
	public function testPurchaseShouldBeSuccessfulWithoutCurrencyCode() {
	  unset($this->transactionAttribs['currency_code']);
		$transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.0, $this->transactionAttribs);

		$this->assertTrue( $transaction->isSuccess() );
	  $this->assertEquals( $this->transactionAttribs['description'], $transaction->description );
	  $this->assertEquals( $this->transactionAttribs['descriptor_name'], $transaction->descriptor_name );
	  $this->assertEquals( $this->transactionAttribs['descriptor_phone'], $transaction->descriptor_phone );
	  $this->assertEquals( $this->transactionAttribs['custom'], $transaction->custom );
	  $this->assertEquals( $this->transactionAttribs['billing_reference'], $transaction->billing_reference );
	  $this->assertEquals( $this->transactionAttribs['customer_reference'], $transaction->customer_reference );
    $this->assertEquals( 'USD', $transaction->currency_code );
	}

	public function testPurchaseFailuresShouldReturnProcessorTransactionDeclined() {
		$transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.02, $this->transactionAttribs);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The card was declined.', $transaction->errors['processor.transaction'][0]->description );
	}
	public function testPurchaseFailuresShouldReturnInputAmountInvalid() {
		$transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.10, $this->transactionAttribs);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['input.amount'][0]->description );
	}

	public function testPurchaseCvvResponsesShouldReturnProcessorCvvResultCodeM() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array('credit_card[cvv]' => '111'));
    $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'M', $transaction->cvvResultCode );
	}
	public function testPurchaseCvvResponsesShouldReturnProcessorCvvResultCodeN() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array('credit_card[cvv]' => '222'));
    $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'N', $transaction->cvvResultCode );
	}
	public function testPurchaseAvsResponsesShouldReturnProcessorAvsResultCodeY() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array(
      'credit_card[address_1]' => '1000 1st Av',
      'credit_card[address_2]' => '',
      'credit_card[zip]' => '10101'
    ));
    $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'Y', $transaction->avsResultCode );
	}
	public function testPurchaseAvsResponsesShouldReturnProcessorAvsResultCodeZ() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array(
      'credit_card[address_1]' => '',
      'credit_card[address_2]' => '',
      'credit_card[zip]' => '10101'
    ));
    $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'Z', $transaction->avsResultCode );
	}
	public function testPurchaseAvsResponsesShouldReturnProcessorAvsResultCodeN() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array(
      'credit_card[address_1]' => '123 Main St',
      'credit_card[address_2]' => '',
      'credit_card[zip]' => '60610'
    ));
    $transaction = Samurai_Processor::theProcessor()->purchase($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'N', $transaction->avsResultCode );
	}

	public function testAuthorizeShouldBeSuccessful() {
		$transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.0, $this->transactionAttribs);

		$this->assertTrue( $transaction->isSuccess() );
	  $this->assertEquals( $this->transactionAttribs['description'], $transaction->description );
	  $this->assertEquals( $this->transactionAttribs['descriptor_name'], $transaction->descriptor_name );
	  $this->assertEquals( $this->transactionAttribs['descriptor_phone'], $transaction->descriptor_phone );
	  $this->assertEquals( $this->transactionAttribs['custom'], $transaction->custom );
	  $this->assertEquals( $this->transactionAttribs['billing_reference'], $transaction->billing_reference );
	  $this->assertEquals( $this->transactionAttribs['customer_reference'], $transaction->customer_reference );
	}
	public function testAuthorizeFailuresShouldReturnProcessorTransactionDeclined() {
		$transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.02, $this->transactionAttribs);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The card was declined.', $transaction->errors['processor.transaction'][0]->description );
	}
	public function testAuthorizeFailuresShouldReturnInputAmountInvalid() {
		$transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.10, $this->transactionAttribs);
		$this->assertFalse( $transaction->isSuccess() );
		$this->assertEquals( 'The transaction amount was invalid.', $transaction->errors['input.amount'][0]->description );
	}
	
	public function testAuthorizeCvvResponsesShouldReturnProcessorCvvResultCodeM() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array('credit_card[cvv]' => '111'));
    $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'M', $transaction->cvvResultCode );
	}
	public function testAuthorizeCvvResponsesShouldReturnProcessorCvvResultCodeN() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array('credit_card[cvv]' => '222'));
    $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'N', $transaction->cvvResultCode );
	}
	public function testAuthorizeAvsResponsesShouldReturnProcessorAvsResultCodeY() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array(
      'credit_card[address_1]' => '1000 1st Av',
      'credit_card[address_2]' => '',
      'credit_card[zip]' => '10101'
    ));
    $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'Y', $transaction->avsResultCode );
	}
	public function testAuthorizeAvsResponsesShouldReturnProcessorAvsResultCodeZ() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array(
      'credit_card[address_1]' => '',
      'credit_card[address_2]' => '',
      'credit_card[zip]' => '10101'
    ));
    $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'Z', $transaction->avsResultCode );
	}
	public function testAuthorizeAvsResponsesShouldReturnProcessorAvsResultCodeN() {
    $this->paymentMethod = Samurai_TestHelper::createTestPaymentMethod(array(
      'credit_card[address_1]' => '123 Main St',
      'credit_card[address_2]' => '',
      'credit_card[zip]' => '60610'
    ));
    $transaction = Samurai_Processor::theProcessor()->authorize($this->paymentMethod->token, 1.00, $this->transactionAttribs);
    $this->assertTrue( $transaction->isSuccess() );
    $this->assertEquals( 'N', $transaction->avsResultCode );
	}
}
