<?php 
$ajax        = isset($ajax)        ? $ajax				: false;
$redirectUrl = isset($redirectUrl) ? $redirectUrl : '#'; 
$classes     = isset($classes)     ? $classes			: '';

$transaction = isset($transaction) ? $transaction : new Samurai_Transaction();
$paymentMethod = isset($paymentMethod) ? $paymentMethod : new Samurai_PaymentMethod();
?>

<form action="<?php echo Samurai::$site ?>/payment_methods" method="POST" class="samurai <?php echo $classes ?>" <?php echo $ajax ? 'data-samurai-ajax' : '' ?>>
	<fieldset>
		<input name="redirect_url" type="hidden" value="<?php echo $redirectUrl ?>" />
		<input name="merchant_key" type="hidden" value="<?php echo Samurai::$merchantKey ?>" />
		<input name="sandbox" type="hidden" value="<?php echo Samurai::$sandbox ?>" />
		<?php if ($paymentMethod->token): ?><input name="payment_method_token" type="hidden" value="<?php echo $paymentMethod->token ?>" /><?php endif; ?>
	</fieldset>

	<fieldset>
		<div class="field-group" id="credit_card_name_group">
			<div>
				<label for="credit_card_first_name">First name</label>
				<input id="credit_card_first_name" name="credit_card[first_name]" size="30" type="text" value="<?php echo $paymentMethod->firstName ?>" />
			</div>
			<div>
				<label for="credit_card_last_name">Last name</label>
				<input id="credit_card_last_name" name="credit_card[last_name]" size="30" type="text" value="<?php echo $paymentMethod->lastName ?>" />
			</div>
		</div>

		<div class="field-group" id="credit_card_address_group">
			<div>
				<label for="credit_card_address_1">Address 1</label>
				<input class="div-6" id="credit_card_address_1" name="credit_card[address_1]" size="30" type="text" value="<?php echo $paymentMethod->address1 ?>" />
			</div>
			<div>
				<label for="credit_card_address_2">Address 2</label>
				<input class="div-6" id="credit_card_address_2" name="credit_card[address_2]" size="30" type="text" value="<?php echo $paymentMethod->address2 ?>" />
			</div>
		</div>

		<div class="field-group" id="location_group">
			<div>
				<label for="credit_card_city">City</label>
				<input id="credit_card_city" name="credit_card[city]" size="30" type="text" value="<?php echo $paymentMethod->city ?>" />
			</div>
			<div>
				<label for="credit_card_state">State</label>
				<input class="" id="credit_card_state" name="credit_card[state]" size="30" type="text" value="<?php echo $paymentMethod->state ?>" />
			</div>
			<div>
				<label for="credit_card_zip">Zip</label>
				<input class="" id="credit_card_zip" name="credit_card[zip]" size="30" type="text" value="<?php echo $paymentMethod->zip ?>" />
			</div>
		</div>
	</fieldset>

	<fieldset>
		<div class="field-group" id="credit_card_info_group">
			<div>
				<label for="credit_card_card_number">Card Number</label>
				<input id="credit_card_card_number" name="credit_card[card_number]" size="30" type="text" value="<?php echo $paymentMethod->cardNumber ?>" autocomplete="off" />
				<label data-samurai-card-previews class="show-accepted">
					<span class='visa'></span>
					<span class='mastercard'></span>
					<span class='amex'></span>
					<span class='discover'></span>
				</label>
			</div>
			<div id="samurai_card_cvv">
				<label for="credit_card_cvv">CVV</label>
				<input class="div-1" id="credit_card_cvv" name="credit_card[cvv]" size="30" type="text" value="<?php echo $paymentMethod->cvv ?>" autocomplete="off" />
			</div>
		</div>
		<div class="field-group" id="credit_card_expiration">
			<div>
				<label for="credit_card_expiry_month">Expires on</label>
				<select id="credit_card_expiry_month" name="credit_card[expiry_month]">
					<?php foreach (range(1, 12) as $month): ?>
					<option value="<?php echo $month ?>" <?php echo $paymentMethod->expiryMonth == $month ? 'selected' : '' ?>><?php echo sprintf('%02d', $month) ?></option>
					<?php endforeach; ?>
				</select>
				<select id="credit_card_expiry_year" name="credit_card[expiry_year]">
					<?php foreach (range(2011, 2016) as $year): ?>
					<option value="<?php echo $year ?>" <?php echo $paymentMethod->expiryYear == $year ? 'selected' : '' ?>><?php echo $year ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</fieldset>

	<button type='submit' class='button'>Submit Payment</button>
	<span class='loading' style="display: none;"></span>
	<span class='results' style="display: none;"></span>
</form>

