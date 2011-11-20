<?php

class Samurai_Helpers
{
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}

	/*
	 * Converts a string from camelCase to under_score notation.
	 */
	public static function underscore($string) {
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $string));
	}
}
