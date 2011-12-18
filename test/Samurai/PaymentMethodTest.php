<?php
require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Samurai_PaymentMethodTest extends PHPUnit_Framework_TestCase
{
  public function setUp() {
    $this->params = array(
			'first_name'   => 'FirstName',
			'last_name'    => 'LastName',
			'address_1'    => '123 Main St.',
			'address_2'    => 'Apt #3',
			'city'         => 'Chicago',
			'state'        => 'IL',
			'zip'          => '10101',
			'card_number'  => '4111-1111-1111-1111',
			'cvv'          => '123',
			'expiry_month' => '03',
			'expiry_year'  => '2015',
			'custom'			 => array(
				'id'         => 5,
				'reference'  => 'foo'
			)
		);

    $this->updateParams = array(
  		'first_name'   => 'FirstNameX',
  		'last_name'    => 'LastNameX',
  		'address_1'    => '123 Main St.X',
  		'address_2'    => 'Apt #3X',
  		'city'         => 'ChicagoX',
  		'state'        => 'IL',
  		'zip'          => '10101',
  		'card_number'  => '5454-5454-5454-5454',
  		'cvv'          => '456',
  		'expiry_month' => '05',
  		'expiry_year'  => '2016'
  	);
  }

	public function testS2SCreateShouldBeSuccessful() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);

		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( true, $pm->isExpirationValid );
		$this->assertEquals( $this->params['first_name'], $pm->firstName );
		$this->assertEquals( $this->params['last_name'],  $pm->lastName );
		$this->assertEquals( $this->params['address_1'],  $pm->address1 );
		$this->assertEquals( $this->params['address_2'],  $pm->address2 );
		$this->assertEquals( $this->params['city'],   $pm->city );
		$this->assertEquals( $this->params['state'],  $pm->state );
		$this->assertEquals( $this->params['zip'],    $pm->zip );
		$this->assertEquals( substr($this->params['card_number'], -4),  $pm->lastFourDigits );
		$this->assertEquals( $this->params['expiry_month'],   $pm->expiryMonth );
		$this->assertEquals( $this->params['expiry_year'],    $pm->expiryYear );
	}

	public function testS2SCreateFailOnInputCardNumberShouldReturnIsBlank() {
	  $this->params['card_number'] = '';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was blank.', $pm->errors['input.card_number'][0]->description );
	}
	public function testS2SCreateFailOnInputCardNumberShouldReturnTooShort() {
	  $this->params['card_number'] = '4111-1';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was too short.', $pm->errors['input.card_number'][0]->description );
	}
	public function testS2SCreateFailOnInputCardNumberShouldReturnTooLong() {
	  $this->params['card_number'] = '4111-1111-1111-1111-11';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was too long.', $pm->errors['input.card_number'][0]->description );
	}
	public function testS2SCreateFailOnInputCardNumberShouldReturnFailedChecksum() {
	  $this->params['card_number'] = '4111-1111-1111-1234';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was invalid.', $pm->errors['input.card_number'][0]->description );
	}

	public function testS2SCreateFailOnInputCvvShouldReturnTooShort() {
	  $this->params['cvv'] = '1';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The CVV was too short.', $pm->errors['input.cvv'][0]->description );
	}
	public function testS2SCreateFailOnInputCvvShouldReturnTooLong() {
	  $this->params['cvv'] = '111111';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The CVV was too long.', $pm->errors['input.cvv'][0]->description );
	}
	public function testS2SCreateFailOnInputCvvShouldReturnNotNumeric() {
	  $this->params['cvv'] = 'abcd';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The CVV was invalid.', $pm->errors['input.cvv'][0]->description );
	}

	public function testS2SCreateFailOnInputExpiryMonthShouldReturnIsBlank() {
	  $this->params['expiry_month'] = '';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration month was blank.', $pm->errors['input.expiry_month'][0]->description );
	}
	public function testS2SCreateFailOnInputExpiryMonthShouldReturnIsInvalid() {
	  $this->params['expiry_month'] = 'abcd';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration month was invalid.', $pm->errors['input.expiry_month'][0]->description );
	}
	public function testS2SCreateFailOnInputExpiryYearShouldReturnIsBlank() {
	  $this->params['expiry_year'] = '';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration year was blank.', $pm->errors['input.expiry_year'][0]->description );
	}
	public function testS2SCreateFailOnInputExpiryYearShouldReturnIsInvalid() {
	  $this->params['expiry_year'] = 'abcd';
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration year was invalid.', $pm->errors['input.expiry_year'][0]->description );
	}

	public function testS2SUpdateShouldBeSuccessful() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);

		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( true, $pm->isExpirationValid );
		$this->assertEquals( $this->updateParams['first_name'], $pm->firstName );
		$this->assertEquals( $this->updateParams['last_name'],  $pm->lastName );
		$this->assertEquals( $this->updateParams['address_1'],  $pm->address1 );
		$this->assertEquals( $this->updateParams['address_2'],  $pm->address2 );
		$this->assertEquals( $this->updateParams['city'],   $pm->city );
		$this->assertEquals( $this->updateParams['state'],  $pm->state );
		$this->assertEquals( $this->updateParams['zip'],    $pm->zip );
		$this->assertEquals( substr($this->updateParams['card_number'], -4),  $pm->lastFourDigits );
		$this->assertEquals( $this->updateParams['expiry_month'],   $pm->expiryMonth );
		$this->assertEquals( $this->updateParams['expiry_year'],    $pm->expiryYear );
	}
	public function testS2SUpdateShouldBeSuccessfulPreservingSensitiveData() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['card_number'] = '****-****-****-****';
	  $this->updateParams['cvv'] = '***';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);

		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( true, $pm->isExpirationValid );
		$this->assertEquals( $this->updateParams['first_name'], $pm->firstName );
		$this->assertEquals( $this->updateParams['last_name'],  $pm->lastName );
		$this->assertEquals( $this->updateParams['address_1'],  $pm->address1 );
		$this->assertEquals( $this->updateParams['address_2'],  $pm->address2 );
		$this->assertEquals( $this->updateParams['city'],   $pm->city );
		$this->assertEquals( $this->updateParams['state'],  $pm->state );
		$this->assertEquals( $this->updateParams['zip'],    $pm->zip );
		$this->assertEquals( substr($this->params['card_number'], -4),  $pm->lastFourDigits );
		$this->assertEquals( $this->updateParams['expiry_month'],   $pm->expiryMonth );
		$this->assertEquals( $this->updateParams['expiry_year'],    $pm->expiryYear );
	}
	public function testS2SUpdateFailOnInputCardNumberShouldReturnTooShort() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['card_number'] = '4111-1';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was too short.', $pm->errors['input.card_number'][0]->description );
	}
	public function testS2SUpdateFailOnInputCardNumberShouldReturnTooLong() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['card_number'] = '4111-1111-1111-1111-11';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was too long.', $pm->errors['input.card_number'][0]->description );
	}
	public function testS2SUpdateFailOnInputCardNumberShouldReturnFailedChecksum() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['card_number'] = '4111-1111-1111-1234';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The card number was invalid.', $pm->errors['input.card_number'][0]->description );
	}

	public function testS2SUpdateFailOnInputCvvShouldReturnTooShort() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['cvv'] = '1';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The CVV was too short.', $pm->errors['input.cvv'][0]->description );
	}
	public function testS2SUpdateFailOnInputCvvShouldReturnTooLong() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['cvv'] = '111111';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The CVV was too long.', $pm->errors['input.cvv'][0]->description );
	}
	public function testS2SUpdateFailOnInputCvvShouldReturnNotNumeric() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['cvv'] = 'abcd';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( false, $pm->isSensitiveDataValid );
		$this->assertEquals( 'The CVV was invalid.', $pm->errors['input.cvv'][0]->description );
	}

	public function testS2SUpdateFailOnInputExpiryMonthShouldReturnIsBlank() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['expiry_month'] = '';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration month was blank.', $pm->errors['input.expiry_month'][0]->description );
	}
	public function testS2SUpdateFailOnInputExpiryMonthShouldReturnIsInvalid() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['expiry_month'] = 'abcd';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration month was invalid.', $pm->errors['input.expiry_month'][0]->description );
	}
	public function testS2SUpdateFailOnInputExpiryYearShouldReturnIsBlank() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['expiry_year'] = '';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration year was blank.', $pm->errors['input.expiry_year'][0]->description );
	}
	public function testS2SUpdateFailOnInputExpiryYearShouldReturnIsInvalid() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $this->updateParams['expiry_year'] = 'abcd';
		$this->paymentMethod->updateAttributes($this->updateParams);
		$this->paymentMethod->save();
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);
		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( false, $pm->isExpirationValid );
		$this->assertEquals( 'The expiration year was invalid.', $pm->errors['input.expiry_year'][0]->description );
	}

	public function testFindShouldBeSuccessful() {
		$this->paymentMethod = Samurai_PaymentMethod::create($this->params);
	  $pm = Samurai_PaymentMethod::find($this->paymentMethod->token);

		$this->assertEquals( true, $pm->isSensitiveDataValid );
		$this->assertEquals( true, $pm->isExpirationValid );
		$this->assertEquals( $this->params['first_name'], $pm->firstName );
		$this->assertEquals( $this->params['last_name'],  $pm->lastName );
		$this->assertEquals( $this->params['address_1'],  $pm->address1 );
		$this->assertEquals( $this->params['address_2'],  $pm->address2 );
		$this->assertEquals( $this->params['city'],   $pm->city );
		$this->assertEquals( $this->params['state'],  $pm->state );
		$this->assertEquals( $this->params['zip'],    $pm->zip );
		$this->assertEquals( substr($this->params['card_number'], -4),  $pm->lastFourDigits );
		$this->assertEquals( $this->params['expiry_month'],   $pm->expiryMonth );
		$this->assertEquals( $this->params['expiry_year'],    $pm->expiryYear );
	}

	public function testFindShouldFailOnAnInvalidToken() {
	  $pm = Samurai_PaymentMethod::find('abc123');
		$this->assertEquals( "Couldn't find PaymentMethod with token = abc123", $pm->errors['system.general'][0]->description );
	}
}
