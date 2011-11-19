<?php

class Samurai_Processor 
{
  public $processorToken;
  private static $theProcessor;
  private $connection;

  /*
   * Returns the default processor specified by `processorToken` if you passed it into `Samurai::setup`.
   */
  public static function theProcessor() {
    if (!isset(self::$theProcessor)) {
      self::$theProcessor = new self(Samurai::$processorToken);
    }
    return self::$theProcessor;
  }

  /*
   * Returns a `Samurai_Processor` object for the specified `$processorToken`.
   */
  public static function find($processorToken) {
    return new self($processorToken);
  }

  //public static function purchase($paymentMethodToken, $amount, $options = array()) {
    //return self::theProcessor()->purchase($paymentMethodToken, $amount, $options = array());
  //}

  //public static function authorize($paymentMethodToken, $amount, $options = array()) {
    //return self::theProcessor()->authorize($paymentMethodToken, $amount, $options = array());
  //}

  public function __construct ($processorToken) {
    $this->processorToken = $processorToken;
    $this->connection = Samurai_Connection::instance();
  }

  /*
   * Convenience method to authorize and capture a payment_method for a particular amount in one transaction.
   * Parameters:
   *
   * * `$paymentMethodToken`: token identifying the payment method to authorize
   * * `$amount`: amount to authorize
   * * `$options`: an optional array of additional values to pass in. Accepted values are:
   *   * `descriptor`: descriptor for the transaction
   *   * `custom`: custom data, this data does not get passed to the processor, it is stored within `api.samurai.feefighters.com` only
   *   * `customer_reference`: an identifier for the customer, this will appear in the processor if supported
   *   * `billing_reference`: an identifier for the purchase, this will appear in the processor if supported
   *
   * Returns a Samurai_Transaction containing the processor's response.
   */
  public function purchase($paymentMethodToken, $amount, $options = array()) {
    $options = array_merge($options, array('payment_method_token' => $paymentMethodToken, 'amount' => $amount));
    list($error, $response) = $this->connection->post($this->pathFor('purchase'), $this->prepareTransactionData($options));
    return $this->handleResponse($error, $response);
  }

  /*
   * Authorize a payment_method for a particular amount.
   * Parameters:
   *
   * * `$paymentMethodToken`: token identifying the payment method to authorize
   * * `$amount`: amount to authorize
   *
   * * `$options`: an optional array of additional values to pass in. Accepted values are:
   *   * `descriptor`: descriptor for the transaction
   *   * `custom`: custom data, this data does not get passed to the processor, it is stored within api.samurai.feefighters.com only
   *   * `customer_reference`: an identifier for the customer, this will appear in the processor if supported
   *   * `billing_reference`: an identifier for the purchase, this will appear in the processor if supported
   *
   * Returns a Samurai.Transaction containing the processor's response.
   */
  public function authorize($paymentMethodToken, $amount, $options = array()) {
    $options = array_merge($options, array('payment_method_token' => $paymentMethodToken, 'amount' => $amount));
    list($error, $response) = $this->connection->post($this->pathFor('authorize'), $this->prepareTransactionData($options));
    return $this->handleResponse($error, $response);
  }

  /*
   * Returns a new `Samurai_Transaction` object, associated with the 
   * request.
   */
  private function handleResponse($error, $response) {
    $transaction = new Samurai_Transaction();
    $transaction->handleResponse($error, $response);
    return $transaction;
  }

  /*
   * Wraps transaction data in an additional transaction object,
   * according to spec.
   */
  private function prepareTransactionData($data) {
    return array('transaction' => $data);
  }

  /*
   * Returns the API endpoint that should be used for `method`.
   */
  private function pathFor($method) {
    $root = 'processors/' . $this->processorToken;
    return $root . '/' . $method . '.xml';
  }
}
