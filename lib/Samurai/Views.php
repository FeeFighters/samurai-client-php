<?php

/*
 * The Samurai_Views class provides a couple of useful helper methods 
 * that render payment forms and error markup.
 */
class Samurai_Views 
{
	private static $_viewsPath;

	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}

	private static function viewsPath() {
		if (empty(self::$_viewsPath)) {
			self::$_viewsPath = realpath(dirname(__FILE__) . '/../../views');
		}
		
		return self::$_viewsPath;
	}

	/*
	 * Checks a payment method or transaction object for errors and 
	 * generates an HTML list with the descriptions of those errors.
	 *
	 * Parameters:
	 * 
	 * The `$vars` array gets extracted when a template is rendered. To 
	 * make sure you get the expected output, pass the following 
	 * parameters as array items:
	 *
	 * - `paymentMethod`: An instance of Samurai_PaymentMethod that could 
	 *		contain errors. (optional)
	 * - `transaction`: An instance of Samurai_Transaction that could 
	 *		contain errors. (optional)
	 *
	 * The second `$toString` parameter is optional and setting it to 
	 * `true` will render the requested template to a string and return 
	 * it.
	 *
	 */

	public static function renderErrors($vars = array(), $toString = false) {
		return self::renderView('/application/_Errors.php', $vars, $toString);
	}

	/*
	 * Renders a standard Samurai payment form, similar to the one you'd 
	 * get with Samurai.js.
	 *
	 * Parameters:
	 *
	 * The `$vars` array gets extracted when a template is rendered. To 
	 * make sure you get the expected output, pass the following 
	 * parameters as array items:
	 *
	 * - `redirectUrl`: This parameter tells Samurai where to redirect the 
	 *		user's browser after a payment_method_token has been generated. 
	 * 		This URL will get `?payment_method_token=[the token]` appended to 
	 * 		the end of it. (required)
	 * - `paymentMethod`: An instance of Samurai_PaymentMethod, which will 
	 *		be used to fill in existing payment information (e.g. when you 
	 * 		present the form after a validation error). (optional)
	 * - `ajax`: When this option is set to `true`, a `data-samurai-ajax` 
	 *		attribute will be added to the form, so it can be used with 
	 *		Samurai.js. (optional)
	 * - `classes`: Use this parameter to add additional class names on 
	 *		the <form> tag. (optional)
	 *
	 * The second `$toString` parameter is optional and setting it to 
	 * `true` will render the requested template to a string and return 
	 * it.
	 *
	 */
	public static function renderPaymentForm($vars = array(), $toString = false) {
		return self::renderView('/application/_PaymentForm.php', $vars, $toString);
	}

	private static function renderView($view, $vars = array(), $toString = false) {
		if (!empty($vars) && is_array($vars)) {
			extract($vars);
		}

		if ($toString) {
			ob_start();
		}

		include self::viewsPath() . $view;

		if ($toString) {
			return ob_get_clean();
		}
	}
}
