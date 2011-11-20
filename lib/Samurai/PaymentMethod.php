<?php

class Samurai_PaymentMethod 
{
  private static $knownAttributes = array( 'first_name'
                                         , 'last_name'
                                         , 'address_1'
                                         , 'address_2'
                                         , 'city'
                                         , 'state'
                                         , 'zip'
                                         , 'card_number'
                                         , 'cvv'
                                         , 'expiry_month'
                                         , 'expiry_year'
                                         , 'custom'
                                         , 'sandbox'
                                         );

  /* -- Properties -- */

  private $isNew;
  private $attributes = array();
  private $connection;

  public $messages = array();
  public $errors = array();

  /* -- Class Methods -- */

  /*
   * Creates a new payment method and returns it.
   * Parameters:
   *   * `$attributes`: An object containing the personal and
   *   credit card information you want stored with this new payment
   *   method.
   */
  public static function create($attributes = array()) {
    $paymentMethod = new self($attributes);
    $paymentMethod->save();
    return $paymentMethod;
  }

  /*
   * Retrieves the payment method identified by `token`.
   */
  public static function find($token) {
    $paymentMethod = new self(array('payment_method_token' => $token));
    $paymentMethod->refresh();
    return $paymentMethod;
  }

  /*
   * Creates a new payment method object, but does not save it to the
   * Samurai servers. Use the `save()` method on the returned payment method
   * object to save it.
   */
  public function __construct($attributes = array()) {
    if(Samurai::$sandbox) {
      $attributes = array_merge($attributes, array('sandbox' => true));
    }

    $this->attributes = array_merge($this->attributes, $attributes);
    $this->isNew = !isset($attributes['payment_method_token']);
    $this->connection = Samurai_Connection::instance();
  }

  /* -- Methods -- */

  /*
   * Retains the payment method on `api.samurai.feefighters.com`. Retain a payment method if
   * it will not be used immediately. 
   */
  public function retain() {
    list($error, $response) = $this->connection->post($this->pathFor('retain'));
    $this->handleResponse($error, $response);
  }

  /*
   * Redacts sensitive information from the payment method, rendering it unusable.
   */
  public function redact() {
    list($error, $response) = $this->connection->post($this->pathFor('redact'));
    $this->handleResponse($error, $response);
  }

  /*
   * Updates the internal `attributes` array of the payment method with 
   * information from the Samurai API.
   */
  public function refresh() {
    list($error, $response) = $this->connection->get($this->pathFor('show'));
    $this->handleResponse($error, $response);
  }

  /*
   * Saves the payment method to the Samurai servers if this is a new
   * payment method, or updates the information for an existing payment
   * method.
   */
  public function save() {
    if ($this->isNew) {
      list($error, $response) = $this->connection->post(
        $this->pathFor('create'), 
        array('payment_method' => $this->sanitizedAttributes())
      );
      $this->handleResponse($error, $response);
      $this->isNew = false;
    } else {
      list($error, $response) = $this->connection->put(
        $this->pathFor('update'), 
        array('payment_method' => $this->sanitizedAttributes())
      );
      $this->handleResponse($error, $response);
    }
  }
  
  /* -- Helpers -- */

  /*
   * Parses the Samurai response for messages (info or error) and updates 
   * the current transaction's information. If an HTTP error is 
   * encountered, it will be thrown from this method.
   */
  private function handleResponse($error, $response) {
    if ($error) {
      $this->attributes['success'] = false;
      throw $error;
    } else {
      if (isset($response) && isset($response['payment_method'])) {
        $this->updateAttributes($response['payment_method']);
      }
    }

    if(!empty($response)) {
      $this->processResponseMessages($response);
    }
  }

  /*
   * Finds message blocks in the Samurai response, creates a `Samurai_Message`
   * object for each one and stores them in either the `messages` or the
   * `errors` internal array, depending on the message type.
   */
  private function processResponseMessages($response = array()) {
    $messages = self::extractMessagesFromResponse($response);
    $this->messages = array();
    $this->errors = array();
    
    foreach ($messages as $message) {
      $message['subclass'] = isset($message['subclass']) ? $message['subclass'] : $message['class'];
      $message['$t']       = isset($message['$t'])       ? $message['$t']       : null;
      $message['context']  = empty($message['context'])  ? 'system.general'     : $message['context'];

      $m = new Samurai_Message($message['subclass'], $message['context'], $message['key'], $message['$t']);

      if ($message['subclass'] === 'error') {
        if (isset($this->errors[$message['context']])) {
          $this->errors[$message['context']][] = $m;
        } else {
          $this->errors[$message['context']] = array($m);
        }
      } else {
        if (isset($this->messages[$message['context']])) {
          $this->messages[$message['context']][] = $m;
        } else {
          $this->messages[$message['context']] = array($m);
        }
      }
    }
  }

  /*
   * Finds all messages returned in a Samurai response, regardless of
   * what part of the response they were in.
   */
  private static function extractMessagesFromResponse($response = array()) {
    $messages = array();

    foreach ($response as $key => $value) {
      if ($key === 'messages' && is_array($value)) {
        $messages = array_merge($messages, $value);
      } elseif (is_array($value)) {
        $res = self::extractMessagesFromResponse($value);
        if(!empty($res)) {
          $messages = array_merge($messages, $res);
        }
      }
    }

    return $messages;
  }

  /*
   * Returns the API endpoint that should be used for `method`.
   */
  private function pathFor($method) {
    $root = 'payment_methods';

    switch ($method) {
      case 'create':
        return $root . '.json';
      case 'update':
        return $root . '/' . $this->token . '.json';
      case 'show':
        return $root . '/' . $this->token . '.xml';
      default:
        return $root . '/' . $this->token . '/' . $method . '.xml';
    }
  }

  /*
   * Updates the internal `attributes` array with newly returned information.
   */
  public function updateAttributes($attributes = array()) {
    $this->attributes = array_merge($this->attributes, $attributes);
  }

  /*
   * Makes sure that the payment method attributes we send to the Samurai API are part
   * of the `$knownAttributes` array.
   */
  private function sanitizedAttributes() {
    $attr = array();
    foreach ($this->attributes as $key => $val) {
      if (in_array($key, self::$knownAttributes)) {
        $attr[$key] = $val;
      }
    }
    return $attr;
  }
  
  /* -- Accessors -- */

  /*
   * Makes sure all properties of the internal `attributes` array can be 
   * accessed with their camelized versions from the outside. E.g.:
   *
   *   $pm->attributes['first_name'] === $pm->firstName;
   *
   */
  public function __get($prop) {
    switch($prop) {
      case 'token':
        return $this->attributes['payment_method_token'];
      case 'customJsonData':
        if (isset($this->attributes['custom']) && is_string($this->attributes['custom'])) {
          $custom = json_decode($this->attributes['custom'], true);
        } 
        return empty($custom) ? array() : $custom;
      default:
        $underscoredProp = Samurai_Helpers::underscore($prop);
        if (isset($this->attributes[$underscoredProp])) {
          return $this->attributes[$underscoredProp];
        } else {
          return null;
        }
    }
  }

  public function __set($prop, $value) {
    $underscoredProp = Samurai_Helpers::underscore($prop);
    if(in_array($underscoredProp, self::$knownAttributes)) { 
      $this->attributes[$underscoredProp] = $value;
    }
  }
}
