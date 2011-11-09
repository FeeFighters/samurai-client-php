<?php
class SamuraiTestSuite {

  public function createPaymentMethod ( ) {
    
    $params = array();
    $params['credit_card[first_name]']    = 'John';
    $params['credit_card[last_name]']     = 'Smith';
    $params['credit_card[address_1]']     = '1000 1st Av';
    $params['credit_card[address_2]']     = '';
    $params['credit_card[city]']          = 'Chicago';
    $params['credit_card[state]']         = 'IL';
    $params['credit_card[zip]']           = '10101';
    $params['credit_card[card_type]']     = 'visa';
    $params['credit_card[card_number]']   = '4111111111111111';
    $params['credit_card[cvv]']           = '111';
    $params['credit_card[expiry_month]']  = '09';
    $params['credit_card[expiry_year]']   = '13';
    $params['redirect_url']               = 'http://example.com/nowhere';
    $params['merchant_key']               = SAMURAI_MERCHANT_KEY;
    $params['sandbox']                    = true;    

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'https://api.samurai.feefighters.com/v1/payment_methods' );
    curl_setopt( $ch, CURLOPT_USERAGENT, "FeeFighter's Samurai PHP Client v".Samurai::VERSION." Test Suite" );
    curl_setopt( $ch, CURLOPT_HEADER, TRUE );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
    curl_setopt( $ch, CURLOPT_POST, TRUE );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($params) );
    $headers = curl_exec( $ch );

    preg_match( '#Location: .*\?payment_method_token=([a-f0-9]+)#', $headers, $match );
    return SamuraiPaymentMethod::fetchByToken( $match[1] ); 
  }

  public function createTransaction( ) {
    $payment_method = SamuraiTestSuite::createPaymentMethod();
    $transaction = new SamuraiTransaction();
    $transaction->setAmount( 60.00 );
    $transaction->setCurrencyCode( 'USD' );
    $transaction->setPaymentMethodToken( $payment_method->getToken() );
    $transaction->setBillingReference( 'Billing Reference 1234' );
    $transaction->setCustomerReference( 'Customer #1' );
    $processor = new SamuraiProcessor( SAMURAI_PROCESSOR_TOKEN );
    $new_transaction = $transaction->purchase( $processor );    
    return $new_transaction;
  }  

}
?>
