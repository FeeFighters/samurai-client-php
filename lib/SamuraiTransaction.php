<?

  class SamuraiTransaction {

    private $token;
    private $type;
    private $amount;
    private $currency_code;
    private $payment_method_token;
    private $billing_reference;
    private $customer_reference;
    private $descriptor;
    private $custom;

    public function purchase ( ) {

    }

    public function authorize ( ) {

    }

    public function capture ( ) {

    }

    public function void ( ) {

    }

    public function credit ( $amount ) {

    }

    public static function fetchById ( $reference_id ) {

      $url = sprintf( '/transactions/%s.xml', $reference_id );
      $samurai_request = new SamuraiRequest( $url );
      $samurai_response = $samurai_request->send();

      #var_dump( $samurai_response );

      $oSamuraiTransaction = new SamuraiTransaction();
      return $oSamuraiTransaction();
    }

  }

?>
