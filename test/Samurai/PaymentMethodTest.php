<?php

require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Samurai_PaymentMethodTest extends PHPUnit_Framework_TestCase 
{
  public function setUp() {
		$this->paymentMethod = Samurai_PaymentMethod::create(array(
			'first_name'   => 'sean',
			'last_name'    => 'harper',
			'city'         => 'Chicago',
			'zip'          => '53211',
			'expiry_month' => 03,
			'cvv'          => '123',
			'card_number'  => '4111111111111111',
			'address_1'    => '1240 W Monroe #1',
			'address_2'    => '',
			'expiry_year'  => '2015',
			'state'        => 'IL',
			'custom'			 => array(
				'id'         => 5,
				'reference'  => 'foo'
			)
		));
  }

	public function testPaymentMethodToken() {
		$this->assertInternalType('string', $this->paymentMethod->token);
		$this->assertEquals(24, strlen($this->paymentMethod->token));
	}

	public function testPropertyAccess() {
		$this->assertEquals('sean', $this->paymentMethod->firstName);
		$this->assertEquals('harper', $this->paymentMethod->lastName);
		$this->assertEquals('Chicago', $this->paymentMethod->city);
		$this->assertEquals(true, $this->paymentMethod->isSensitiveDataValid);

		$this->paymentMethod->firstName = 'John';
		$this->paymentMethod->lastName = 'Smith';

		$this->assertEquals('John', $this->paymentMethod->firstName);
		$this->assertEquals('Smith', $this->paymentMethod->lastName);
	}

	public function testCustomDataSerialization() {
		$this->assertInternalType('array', $this->paymentMethod->customJsonData);
		$this->assertEquals(5, $this->paymentMethod->customJsonData['id']);
		$this->assertEquals('foo', $this->paymentMethod->customJsonData['reference']);
	}

	public function testFindPaymentMethod() {
		$found = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals($found->token, $this->paymentMethod->token);
	}

	public function testRetainPaymentMethod() {
		$this->paymentMethod->retain();
		$this->assertTrue($this->paymentMethod->isRetained);
	}

	public function testRedactPaymentMethod() {
		$this->paymentMethod->redact();
		$this->assertTrue($this->paymentMethod->isRedacted);
	}

	public function testPaymentMethodErrors() {
		$paymentMethod = Samurai_PaymentMethod::create(array(
			'first_name'   => 'sean',
			'last_name'    => 'harper',
			'city'         => 'Chicago',
			'zip'          => '53211',
			'expiry_month' => 03,
			'cvv'          => '123',
			'card_number'  => null,
			'address_1'    => '1240 W Monroe #1',
			'address_2'    => '',
			'expiry_year'  => '2015',
			'state'        => 'IL',
			'custom'			 => array(
				'id'         => 5,
				'reference'  => 'foo'
			)
		));
		$this->assertFalse($paymentMethod->isSensitiveDataValid);
		$this->assertInternalType('array', $paymentMethod->errors);
		$this->assertArrayHasKey('input.card_number', $paymentMethod->errors);
		$this->assertEquals(2, count($paymentMethod->errors['input.card_number']));
		$this->assertInstanceOf('Samurai_Message', $paymentMethod->errors['input.card_number'][0]);
		$this->assertEquals('error',  $paymentMethod->errors['input.card_number'][0]->subclass);
		$this->assertEquals('input.card_number',  $paymentMethod->errors['input.card_number'][0]->context);
		$this->assertEquals('not_numeric',        $paymentMethod->errors['input.card_number'][0]->key);
	}
}
