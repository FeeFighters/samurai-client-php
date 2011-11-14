<?php

  class SamuraiTransaction {

    public $amount;
    public $currency_code;
    public $payment_method_token;
    public $billing_reference;
    public $customer_reference;
    public $descriptor;
	public $success;
    public $custom;
    public $token;
    public $reference_id;
    public $transaction_type;
    public $created_at;
    public $processor_token;
    public $payment_method;
    public $processor_response;

    public function getReferenceId ( ) {
      return $this->reference_id;
    }
    
    public function setReferenceId ( $reference_id ) {
      $this->reference_id = $reference_id;
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
    
    public function setCreatedAt ( $created_at ) {
      $this->created_at = $created_at;
    }

    public function getUpdatedAt ( ) {
      return $this->updated_at;
    }
    
    public function setUpdatedAt ( $updated_at ) {
      $this->updated_at = $updated_at;
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

    public function getCustom ( ) {
      return $this->custom;
    }

    public function setCustom ( $custom ) {
      $this->custom = $custom;
    }

    public function getProcessorResponse ( ) {
      return $this->processor_response;
    }

    public function setProcessorResponse ( $response ) {
      $this->processor_response = $processor_response;
    }
    
    public function getSuccess ( ) {
      return $this->success;
    }

    public function setSuccess ( $success ) {
      $this->success = $success;
    }
    
    public function getTransactionType ( ) {
      return $this->transaction_type;
    }

    public function setTransactionType ( $transaction_type ) {
      $this->transaction_type = $transaction_type;
    }
    
    public function getTransactionToken ( ) {
      return $this->transaction_token;
    }

    public function setTransactionToken ( $transaction_token ) {
      $this->transaction_token = $transaction_token;
    }
    
    public function getProcessorToken ( ) {
      return $this->processor_token;
    }

    public function setProcessorToken ( $processor_token ) {
      $this->processor_token = $processor_token;
    }
    
    public function getPaymentMethod ( ) {
      return $this->payment_method;
    }

    public function setPaymentMethod ( $payment_method ) {
      $this->payment_method = $payment_method;
    }


    function transactionFromResponse( $samurai_response ) {
      $samurai_transaction = new SamuraiTransaction();
      $samurai_transaction->reference_id = $samurai_response->getField( 'reference_id' );
      $samurai_transaction->token = $samurai_response->getField( 'transaction_token' );
      $samurai_transaction->created_at = $samurai_response->getField( 'created_at' );
      $samurai_transaction->success = $samurai_response->getField( 'success' );      
      $samurai_transaction->currency_code = $samurai_response->getField( 'currency_code' );
      $samurai_transaction->custom = $samurai_response->getField( 'custom' );
      $samurai_transaction->billing_reference = $samurai_response->getField( 'billing_reference' );
      $samurai_transaction->customer_reference = $samurai_response->getField( 'customer_reference' );
      $samurai_transaction->descriptor = $samurai_response->getField( 'descriptor' );
      $samurai_transaction->amount = $samurai_response->getField( 'amount' );
      $samurai_transaction->transaction_type = $samurai_response->getField( 'transaction_type' );
      $samurai_transaction->processor_token = $samurai_response->getField( 'processor_token' );
      $samurai_transaction->payment_method = $samurai_response->getField( 'payment_method' );
      $samurai_transaction->processor_response = $samurai_response;
      return $samurai_transaction;
    }

    public function purchase ( $samurai_processor ) {
      $params = array();
      $params['transaction'] = array();
      $params['transaction']['amount'] = $this->amount;
      $params['transaction']['currency_code'] = $this->currency_code;
      $params['transaction']['payment_method_token'] = $this->payment_method_token;
      $params['transaction']['billing_reference'] = $this->billing_reference;
      $params['transaction']['customer_reference'] = $this->customer_reference;
      $params['transaction']['descriptor'] = $this->descriptor;
      $params['transaction']['custom'] = $this->custom;
      $url = sprintf( '/processors/%s/purchase.xml', $samurai_processor->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

    public function authorize ( $samurai_processor ) {
      $params = array();
      $params['transaction'] = array();
      $params['transaction']['amount'] = $this->amount;
      $params['transaction']['currency_code'] = $this->currency_code;
      $params['transaction']['payment_method_token'] = $this->payment_method_token;
      $params['transaction']['payment_method_token'] = $this->payment_method_token;
      $params['transaction']['billing_reference'] = $this->billing_reference;
      $params['transaction']['descriptor'] = $this->descriptor;
      $params['transaction']['custom'] = $this->custom;
      $url = sprintf( '/processors/%s/authorize.xml', $samurai_processor->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

    public function capture ( ) {
      $params = array();
      $params['transaction'] = array();
      $params['transaction']['amount'] = $this->getAmount();
      $url = sprintf( '/transactions/%s/capture.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

    public function void ( ) {
      $url = sprintf( '/transactions/%s/void.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST' );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

    public function credit ( $amount, &$samurai_response=null ) {
      $params = array();
      $params['amount'] = $amount;
      $url = sprintf( '/transactions/%s/credit.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

    public function reverse ( $amount=null, &$samurai_response=null ) {
      $params = array();
      $params['amount'] = $amount == null ? $this->getAmount() : $amount;
      $url = sprintf( '/transactions/%s/reverse.xml', $this->getToken() );
      $samurai_request = new SamuraiRequest( $url, 'POST', $params );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

    public static function fetchByReferenceId ( $reference_id, &$samurai_response=null ) {
      $url = sprintf( '/transactions/%s.xml', $reference_id );
      $samurai_request = new SamuraiRequest( $url );
      $samurai_response = $samurai_request->send();
      return $this->transactionFromResponse($samurai_response);
    }

  }

?>
