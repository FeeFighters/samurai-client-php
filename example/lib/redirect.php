<?

  if ( ! array_key_exists('payment_method_token',$_GET) )
    die( 'Missing payment_method_token' );

  try {

    require_once dirname(dirname(dirname(__DIR__))).'/samurai_credentials.php';
    require_once dirname(dirname(__DIR__)).'/Samurai.php';

    Samurai::$processor_token = MY_SAMURAI_PROCESSOR_TOKEN;
    Samurai::$merchant_key = MY_SAMURAI_MERCHANT_KEY;
    Samurai::$merchant_password = MY_SAMURAI_MERCHANT_PASSWORD;

    $payment_method_token = $_GET['payment_method_token'];
    $samurai_payment_method = SamuraiPaymentMethod::fetchByToken( $payment_method_token );
   
    if ( $samurai_payment_method->getIsSensitiveDataValid() ) {
      echo 'Successful payment method: '.$samurai_payment_method->getToken();
    } else {
      echo 'Erroneous payment method: '.$samurai_payment_method->getToken()."\n";
      $samurai_messages = $samurai_payment_method->getMessages();
      foreach ( $samurai_messages as $samurai_message ) {
        echo sprintf( "  %s - %s\n", $samurai_message->getContext(), $samurai_message->getKey() );
      }
    }
 
  } catch ( SamuraiException $e ) {

    echo sprintf( "<p style='font-weight:bold'>Caught Samurai Exception: %s</p>\n", $e->getMessage() );
    $samurai_messages = $e->getSamuraiMessages();
    foreach ( $samurai_messages as $i => $samurai_message )
      echo sprintf( "<p>%d. %s [ %s / %s / %s ]</p>\n", $i+1, $samurai_message->getMessage(), $samurai_message->getClass(), $samurai_message->getContext(), $samurai_message->getKey() );

  }

?>
