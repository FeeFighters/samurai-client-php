<?php 
$transaction   = isset($transaction) ? $transaction : new Samurai_Transaction();
$paymentMethod = isset($paymentMethod) ? $paymentMethod : new Samurai_PaymentMethod();
?>

<?php if ($transaction->hasErrors() || $paymentMethod->hasErrors()): ?>
	<div id="error_explanation">
		<h4>This transaction could not be processed:</h4>
		<ul>
		<?php 
		if ($transaction->hasErrors()):
			foreach ($transaction->errors as $context => $errors):
					foreach ($errors as $error): ?>
						<li><?php echo $error->description ?></li>
		<?php
					endforeach;
				endforeach;
			endif; ?>
		<?php 
		if ($paymentMethod->hasErrors()):
			foreach ($paymentMethod->errors as $context => $errors):
					foreach ($errors as $error): ?>
						<li><?php echo $error->description ?></li>
		<?php
					endforeach;
				endforeach;
			endif; ?>
		</ul>
	</div>
<?php endif; ?>
