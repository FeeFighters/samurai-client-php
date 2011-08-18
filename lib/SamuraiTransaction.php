<?

  class SamuraiTransaction {

    private $reference_id;
    private $token;
    private $created_at;
    private $updated_at;
    private $type;
    private $amount;
    private $currency_code;
    private $payment_method_token;
    private $billing_reference;
    private $customer_reference;
    private $descriptor;
    private $custom;

    public function getReferenceId ( ) {
      return $this->reference_id;
    }

    public function getToken ( ) {
      return $this->token;
    }

    public function setToken ( $token ) {
      $this->token = $token;
    }

    public function getCreatedAt ( ) {
      return $this->created_at;
    }

    public function getUpdatedAt ( ) {
      return $this->updated_at;
    }

    public function getAmount ( ) {
      return $this->amount;
    }

    public function setAmount ( $amount ) {
      $this->amount = $amount;
    }

    public function getCurrencyCode ( ) {
      return $this->currency_code;
    }

    public function setCurrencyCode ( $currency_code ) {
      $this->currency_code = $currency_code;
    }

    public function getPaymentMethodToken ( ) {
      return $this->payment_method_token;
    }

    public function setPaymentMethodToken ( $payment_method_token ) {
      $this->payment_method_token = $payment_method_token;
    }

    public function getBillingReference ( ) {
      return $this->billing_reference;
    }

    public function setBillingReference ( $billing_reference ) {
      $this->billing_reference = $billing_reference;
    }

    public function getCustomerReference ( ) {
      return $this->customer_reference;
    }

    public function setCustomerReference ( $customer_reference ) {
      $this->customer_reference = $customer_reference;
    }

    public function getDescriptor ( ) {
      return $this->descriptor;
    }

    public function setDescriptor ( $descriptor ) {
      $this->descriptor = $descriptor;
    }

    public function getCuston ( ) {
      return $this->custom;
    }

    public function setCustom ( $custom ) {
      $this->custom = $custom;
    }

    private function buildPayload ( ) {
      $params = array();
      $params['transaction'] = array();
      $params['transaction']['amount'] = $this->amount;
      $params['transaction']['currency_code'] = $this->currency_code;
      $params['transaction']['payment_method_token'] = $this->payment_method_token; 
      $params['transaction']['billing_reference'] = $this->billing_reference; 
      $params['transaction']['customer_reference'] = $this->customer_reference; 
      $params['transaction']['descriptor'] = $this->descriptor; 
      $params['transaction']['custom'] = $this->custom;
      return $params;
    }

    public function purchase ( $samurai_processor ) {
      $params = $this->buildPayload();
      $params['transaction']['type'] = 'purchase';
      $url = sprintf( '/processors/%s/purchase.xml', $samurai_processor->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();

      $this->reference_id = $samurai_response->getField( 'reference_id' );
      $this->created_at = $samurai_response->getField( 'created_at' );
      $this->token = $samurai_response->getField( 'transaction_token' );
      $this->type = $samurai_response->getField( 'transaction_type' );

      return $samurai_response;
    }

    public function authorize ( $samurai_processor ) {
      $params = $this->buildPayload();
      $params['transaction']['type'] = 'authorize';
      $url = sprintf( '/processors/%s/authorize.xml', $samurai_processor->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();

      $this->reference_id = $samurai_response->getField( 'reference_id' );
      $this->created_at = $samurai_response->getField( 'created_at' );
      $this->token = $samurai_response->getField( 'transaction_token' );
      $this->type = $samurai_response->getField( 'transaction_type' );

      return $samurai_response;
    }

    public function capture ( ) {
      $params = array();
      $params['transaction'] = array();
      $params['transaction']['amount'] = $this->getAmount();
      $url = sprintf( '/transactions/%s/capture.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();

      $this->reference_id = $samurai_response->getField( 'reference_id' );
      $this->created_at = $samurai_response->getField( 'created_at' );
      $this->token = $samurai_response->getField( 'transaction_token' );
      $this->type = $samurai_response->getField( 'transaction_type' );

      return $samurai_response;
    }

    public function void ( ) {
      $url = sprintf( '/transactions/%s/void.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST' );
      $samurai_response = $samurai_request->send();

      $this->type = $samurai_response->getField( 'transaction_type' );

      return $samurai_response;
    }

    public function credit ( $amount, &$samurai_response=null ) {
      $params = array();
      $params['transaction'] = array();
      $params['transaction']['amount'] = $amount;
      $url = sprintf( '/transactions/%s/credit.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();

      $samurai_transaction = new SamuraiTransaction();
      $samurai_transaction->reference_id = $samurai_response->getField( 'reference_id' );
      $samurai_transaction->token = $samurai_response->getField( 'transaction_token' );
      $samurai_transaction->type = $samurai_response->getField( 'transaction_type' );
      $samurai_transaction->created_at = $samurai_response->getField( 'created_at' );
      $samurai_transaction->custom = $samurai_response->getField( 'custom' );
      $samurai_transaction->transaction_type = $samurai_response->getField( 'transaction_type' );
      $samurai_transaction->amount = $samurai_response->getField( 'amount' );
      $samurai_transaction->currency_code = $samurai_response->getField( 'currency_code' );
      $samurai_transaction->processor_token = $samurai_response->getField( 'processor_token' );
      $samurai_transaction->payment_method = $samurai_response->getField( 'payment_method' );
      return $samurai_transaction;
    }

    public static function fetchByReferenceId ( $reference_id, &$samurai_response=null ) {
      $url = sprintf( '/transactions/%s.xml', $reference_id );
      $samurai_request = new SamuraiRequest( $url );
      $samurai_response = $samurai_request->send();
  
      $samurai_transaction = new SamuraiTransaction();
      $samurai_transaction->reference_id = $samurai_response->getField( 'reference_id' );
      $samurai_transaction->token = $samurai_response->getField( 'transaction_token' );
      $samurai_transaction->created_at = $samurai_response->getField( 'created_at' );
      $samurai_transaction->custom = $samurai_response->getField( 'custom' );
      $samurai_transaction->transaction_type = $samurai_response->getField( 'transaction_type' );
      $samurai_transaction->amount = $samurai_response->getField( 'amount' );
      $samurai_transaction->currency_code = $samurai_response->getField( 'currency_code' );
      $samurai_transaction->processor_token = $samurai_response->getField( 'processor_token' );
      $samurai_transaction->payment_method = $samurai_response->getField( 'payment_method' );
      return $samurai_transaction;
    }

  }

?>
