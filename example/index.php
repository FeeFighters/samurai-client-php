<? require_once '../../samurai_credentials.php'; ?>
<style type="text/css">
  form { font-size: 18px; }
  fieldset { border: none; }
  label { display: block; margin: 10px 0 5px; }
  input { display: block; border: 1px solid #CCC; padding: 2px 4px; }
  input[type=submit] { margin-top: 15px; background-color: white; font-size: 18px; padding: 2px 6px; }
</style>

<form action="https://samurai.feefighters.com/v1/payment_methods" method="POST">
  <fieldset>
    <input name="redirect_url" type="hidden" value="<?= sprintf( 'http%s://%s%slib/redirect.php', $_SERVER['REMOTE_ADDR']==443?'s':null, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ); ?>" />
    <input name="merchant_key" type="hidden" value="<?= MY_SAMURAI_MERCHANT_KEY; ?>" />

    <!-- Before populating the ‘custom’ parameter, remember to escape reserved xml characters 
         like <, > and & into their safe counterparts like &lt;, &gt; and &amp; -->
    <input name="custom" type="hidden" value="Any value you want us to save with this payment method" />

    <label for="credit_card_first_name">First name</label>
    <input id="credit_card_first_name" name="credit_card[first_name]" type="text" />

    <label for="credit_card_last_name">Last name</label>
    <input id="credit_card_last_name" name="credit_card[last_name]" type="text" />

    <label for="credit_card_address_1">Address 1</label>
    <input id="credit_card_address_1" name="credit_card[address_1]" type="text" />

    <label for="credit_card_address_2">Address 2</label>
    <input id="credit_card_address_2" name="credit_card[address_2]" type="text" />

    <label for="credit_card_city">City</label>
    <input id="credit_card_city" name="credit_card[city]" type="text" />

    <label for="credit_card_state">State</label>
    <input id="credit_card_state" name="credit_card[state]" type="text" />

    <label for="credit_card_zip">Zip</label>
    <input id="credit_card_zip" name="credit_card[zip]" type="text" />

    <label for="credit_card_card_type">Card Type</label>
    <select id="credit_card_card_type" name="credit_card[card_type]">
      <option value="visa">Visa</option>
      <option value="master">MasterCard</option>
    </select>

    <label for="credit_card_card_number">Card Number</label>
    <input id="credit_card_card_number" name="credit_card[card_number]" type="text" />

    <label for="credit_card_verification_value">Security Code</label>
    <input id="credit_card_verification_value" name="credit_card[cvv]" type="text" />

    <label for="credit_card_month">Expiration Month</label>
    <input id="credit_card_month" name="credit_card[expiry_month]" type="text" />

    <label for="credit_card_month">Expiration Year</label>
    <input id="credit_card_year" name="credit_card[expiry_year]" type="text" />

    <input type="submit" value="Submit Payment" />
  </fieldset>
</form>
