<?

  class SamuraiPaymentMethod {

    private $token;
    private $created_at;
    private $updated_at;
    private $custom;
    private $is_retained;
    private $is_redacted;
    private $is_sensitive_data_valid;
    private $last_four_digits;
    private $card_type;
    private $first_name;
    private $last_name;
    private $expiry_month;
    private $expiry_year;
    private $address_1;
    private $address_2;
    private $city;
    private $state;
    private $zip;
    private $country;

    private $messages;

    public function getToken ( ) {
      return $this->token;
    }

    public function getCreatedAt ( ) {
      return $this->created_at;
    }

    public function getUpdatedAt ( ) {
      return $this->updated_at;
    }

    public function getCustom ( ) {
      return $this->custom;
    }

    public function getIsRetained ( ) {
      return $this->is_retained;
    }

    public function getIsRedacted ( ) {
      return $this->is_redacted;
    }

    public function getIsSensitiveDataValid ( ) {
      return $this->is_sensitive_data_valid;
    }

    public function getLastFourDigits ( ) {
      return $this->last_four_digits;
    }

    public function getCardType ( ) {
      return $this->card_type;
    }

    public function getFirstName ( ) {
      return $this->first_name;
    }

    public function getLastName ( ) {
      return $this->last_name;
    }

    public function getExpiryMonth ( ) {
      return $this->expiry_month;
    }

    public function getExpiryYear ( ) {
      return $this->expiry_year;
    }

    public function getAddress1 ( ) {
      return $this->address_1;
    }

    public function getAddress2 ( ) {
      return $this->address_2;
    }

    public function getCity ( ) {
      return $this->city;
    }

    public function getState ( ) {
      return $this->state;
    }

    public function getZip ( ) {
      return $this->zip;
    }

    public function getCountry ( ) {
      return $this->country;
    }

    public function getMessages ( ) {
      return $this->messages;
    }

    public function retain ( ) {

      $url = sprintf( '/payment_methods/%s/retain.xml', $this->token );
      $samurai_request = new SamuraiRequest( $url, 'POST' );
      $samurai_response = $samurai_request->send();

      var_dump( $samurai_response );

    }

    public function redact ( ) {

      $url = sprintf( '/payment_methods/%s/redact.xml', $this->token );
      $samurai_request = new SamuraiRequest( $url, 'POST' );
      $samurai_response = $samurai_request->send();

      var_dump( $samurai_response );

    }

    public static function fetchByToken ( $token ) {
      $url = sprintf( '/payment_methods/%s.xml', $token );
      $samurai_request = new SamuraiRequest( $url );
      $samurai_response = $samurai_request->send();

      $samurai_payment_method = new SamuraiPaymentMethod();
      $samurai_payment_method->token                   = $samurai_response->getField( 'payment_method_token' );
      $samurai_payment_method->created_at              = $samurai_response->getField( 'created_at' );
      $samurai_payment_method->updated_at              = $samurai_response->getField( 'updated_at' );
      $samurai_payment_method->custom                  = $samurai_response->getField( 'custom' );
      $samurai_payment_method->is_retained             = $samurai_response->getField( 'is_retained' );
      $samurai_payment_method->is_redacted             = $samurai_response->getField( 'is_redacted' );
      $samurai_payment_method->is_sensitive_data_valid = $samurai_response->getField( 'is_sensitive_data_valid' );
      $samurai_payment_method->last_four_digits        = $samurai_response->getField( 'last_four_digits' );
      $samurai_payment_method->card_type               = $samurai_response->getField( 'first_name' );
      $samurai_payment_method->last_name               = $samurai_response->getField( 'last_name' );
      $samurai_payment_method->expiry_month            = $samurai_response->getField( 'expiry_month' );
      $samurai_payment_method->expiry_year             = $samurai_response->getField( 'expiry_year' );
      $samurai_payment_method->address_1               = $samurai_response->getField( 'address_1' );
      $samurai_payment_method->address_2               = $samurai_response->getField( 'address_2' );
      $samurai_payment_method->city                    = $samurai_response->getField( 'city' );
      $samurai_payment_method->state                   = $samurai_response->getField( 'state' );
      $samurai_payment_method->zip                     = $samurai_response->getField( 'zip' );
      $samurai_payment_method->country                 = $samurai_response->getField( 'country' );
  
      $samurai_payment_method->messages                = $samurai_response->getMessages();

      return $samurai_payment_method;
    }

  }

?>
