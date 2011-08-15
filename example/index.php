<?

  require_once '../../samurai_credentials.php';

  /**
   * $errors will be populated with errors with the input of the credit card form
   */
  $errors = array();
  $samurai_payment_method = null;

  /**
   * Process the payment_method_token if one is being passed via redirect
   */
  if ( array_key_exists('payment_method_token',$_GET) ) {
    try {

      /**
       * Include the Samurai library and initialize required settings
       */
      require_once '../Samurai.php';
      Samurai::$merchant_key = MY_SAMURAI_MERCHANT_KEY;
      Samurai::$merchant_password = MY_SAMURAI_MERCHANT_PASSWORD;

      /**
       * Retrive the associated payment method by token
       */
      $payment_method_token = $_GET['payment_method_token'];
      $samurai_payment_method = SamuraiPaymentMethod::fetchByToken( $payment_method_token );

      if ( $samurai_payment_method->getIsSensitiveDataValid() ) {

        /**
         * The the sensitive data is valid, then a valid payment method has been stored
         */
        printf( '<p style="color:green;">Successful payment method: %s</p>'."<br />", $samurai_payment_method->getToken() );

        /**
         * Call retain on the payment method to store it in Samurai's vault
         *
         * $samurai_response = $samurai_payment_method->retain();
         */
       
        /**
         * Call redact on the payment method to store it in Samurai's vault
         *
         * $samurai_response = $samurai_payment_method->redact();
         */
       
        /**
         * Create a transaction
         */
        $samurai_transaction = new SamuraiTransaction();
        $samurai_transaction->setAmount( 20.00 );
        $samurai_transaction->setCurrencyCode( 'USD' );
        $samurai_transaction->setPaymentMethodToken( $samurai_payment_method->getToken() );

        /**
         * Optional values
         *
         * $samurai_transaction->setBillingReference( $billing_reference );
         * $samurai_transaction->setCustomerReference( $customer_reference );
         * $samurai_transaction->setDescriptor( $descriptor );
         * $samurai_transaction->setCustom( $custom );
         */

        $samurai_processor = new SamuraiProcessor( MY_SAMURAI_PROCESSOR_TOKEN );

        /**
         * Example of a simple purchase
         *
         * $samurai_response = $samurai_transaction->purchase( $samurai_processor );
         */
        
        /**
         * Example of an authorization + capture
         *
         * $samurai_response = $samurai_transaction->authorize( $samurai_processor );
         *
         * $samurai_transaction->setAmount( 18.00 );
         * $samurai_response = $samurai_transaction->capture();
         */

        /**
         * Example of a credit
         *
         * $samurai_response = $samurai_transaction->credit( 9.00 );
         */        

      } else {

        /*
         * If the sensitive data is not valid, you will need to retrieve the problematic fields
         *  and indicate to the user that a problem needs to be fixed
         */

        // @todo Remove line
        // printf( '<p style="color:red">Erroneous payment method: %s</p>', $samurai_payment_method->getToken() );
        $samurai_messages = $samurai_payment_method->getMessages();
  
        foreach ( $samurai_messages as $samurai_message ) {

          if ( $samurai_message->getContextCategory() == 'input' ) {

            /**
             * If the context starts with 'input,' it is recommended to highlight the field to alert the customer
             */
            if ( ! array_key_exists($samurai_message->getContextType(),$errors) )
              $errors[ $samurai_message->getContextType() ] = array();
            $errors[ $samurai_message->getContextType() ][] = $samurai_message->getKey();

          }

          // @todo Remove line
          // printf( "<p style='padding-left:10px;color:red;'>%s - %s</p>", $samurai_message->getContext(), $samurai_message->getKey() );
        }

      }

    } catch ( SamuraiException $e ) {

      printf( "<p style='font-weight:bold'>Caught Samurai Exception: %s</p><br />", $e->getMessage() );
      $samurai_messages = $e->getSamuraiMessages();
      foreach ( $samurai_messages as $i => $samurai_message )
        printf( "<p>%d. %s [ %s / %s / %s ]</p><br />", $i+1, $samurai_message->getMessage(), $samurai_message->getClass(), $samurai_message->getContext(), $samurai_message->getKey() );

      /**
       * It is recommended that you log this error as something wrong has occurred.
       */

    }
  }

?>
<style type="text/css">
  form { font-size: 18px; }
  fieldset { border: none; }
  label { display: block; margin: 10px 0 5px; }
  input { display: block; border: 1px solid #CCC; padding: 2px 4px; }
  input[type=submit] { margin-top: 15px; background-color: white; font-size: 18px; padding: 2px 6px; }
  label.error { font-weight: bold; color: red; }
  input.error { border: 1px solid red; }
</style>

<form action="https://samurai.feefighters.com/v1/payment_methods" method="POST">
  <fieldset>
    <input name="redirect_url" type="hidden" value="<?= sprintf( 'http%s://%s%s', $_SERVER['REMOTE_ADDR']==443?'s':null, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ); ?>" />
    <input name="merchant_key" type="hidden" value="<?= MY_SAMURAI_MERCHANT_KEY; ?>" />

    <!-- Before populating the custom parameter, remember to escape reserved xml characters 
         like <, > and & into their safe counterparts like &lt;, &gt; and &amp; -->
    <input name="custom" type="hidden" value="" />

    <label for="credit_card_first_name">First name</label>
    <input id="credit_card_first_name" name="credit_card[first_name]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getFirstName() : null; ?>" />

    <label for="credit_card_last_name">Last name</label>
    <input id="credit_card_last_name" name="credit_card[last_name]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getLastName() : null; ?>" />

    <label for="credit_card_address_1">Address 1</label>
    <input id="credit_card_address_1" name="credit_card[address_1]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getAddress1() : null; ?>" />

    <label for="credit_card_address_2">Address 2</label>
    <input id="credit_card_address_2" name="credit_card[address_2]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getAddress2() : null; ?>" />

    <label for="credit_card_city">City</label>
    <input id="credit_card_city" name="credit_card[city]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getCity() : null; ?>" />

    <label for="credit_card_state">State</label>
    <input id="credit_card_state" name="credit_card[state]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getState() : null; ?>" />

    <label for="credit_card_zip">Zip</label>
    <input id="credit_card_zip" name="credit_card[zip]" type="text" value="<?= $samurai_payment_method ? $samurai_payment_method->getZip() : null; ?>" />

    <label for="credit_card_card_type">Card Type</label>
    <select id="credit_card_card_type" name="credit_card[card_type]">
      <option></option>
      <option value="visa" <?= $samurai_payment_method && $samurai_payment_method->getCardType() == 'visa' ? 'selected="selected"' : null; ?>>Visa</option>
      <option value="master"<?= $samurai_payment_method && $samurai_payment_method->getCardType() == 'master' ? 'selected="selected"' : null; ?>>MasterCard</option>
    </select>

    <label for="credit_card_card_number" class="<?= array_key_exists('card_number',$errors) ? 'error' : null; ?>">Card Number</label>
    <input id="credit_card_card_number" name="credit_card[card_number]" type="text" class="<?= array_key_exists('card_number',$errors) ? 'error' : null; ?>" value="4111111111111111" />

    <label for="credit_card_verification_value" class="<?= array_key_exists('cvv',$errors) ? 'error' : null; ?>">Security Code</label>
    <input id="credit_card_verification_value" name="credit_card[cvv]" type="text" class="<?= array_key_exists('cvv',$errors) ? 'error' : null; ?>" value="123" />

    <label for="credit_card_month">Expiration Month</label>
    <? $months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ); ?>
    <select name="credit_card[expiry_month]">
    <? foreach ( $months as $i => $month ): $i++; ?>
      <option value="<?= $i; ?>" <?= $samurai_payment_method && $samurai_payment_method->getExpiryMonth() == $i ? 'selected="selected"' : null; ?>><? printf('%02d',$i); ?>. <?= $month; ?></option>
    <? endforeach; ?>
    </select>

    <label for="credit_card_month">Expiration Year</label>
    <select name="credit_card[expiry_year]">
      <option></option>
    <? $last = date( 'Y' ) + 10; ?>
    <? for ( $year=date('Y'); $year<$last; $year++ ): ?>
      <option value="<?= $year; ?>" <?= $samurai_payment_method && $samurai_payment_method->getExpiryYear() == $year ? 'selected="selected"' : null; ?>><?= $year; ?></option>
    <? endfor; ?>
    </select>

    <input type="submit" value="Submit Payment" />
  </fieldset>
</form>
