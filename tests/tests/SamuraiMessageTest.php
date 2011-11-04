<?php
  require_once 'PHPUnit/Autoload.php';

  class SamuraiMessageTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass ( ) {
      require_once '../Samurai.php';
      require_once 'test_samurai_credentials.php';
      require_once 'test_utilities.php';      
      Samurai::$merchant_key = SAMURAI_MERCHANT_KEY;
      Samurai::$merchant_password = SAMURAI_MERCHANT_PASSWORD;
    }

    public function test_processor_transaction_success() {
      $message = new SamuraiMessage('message', 'info', 'processor.transaction', 'success');
      $this->assertEquals($message->getDescription(), 'The transaction was successful.');
    }    
    public function test_processor_transaction_declined() {
      $message = new SamuraiMessage('message', 'error', 'processor.transaction', 'declined');
      $this->assertEquals($message->getDescription(), 'The card was declined.');
    }    
    public function test_processor_issuer_call() {
      $message = new SamuraiMessage('message', 'error', 'processor.issuer', 'call');
      $this->assertEquals($message->getDescription(), 'Call the card issuer for further instructions.');
    }    
    public function test_processor_issuer_unavailable() {
      $message = new SamuraiMessage('message', 'error', 'processor.issuer', 'unavailable');
      $this->assertEquals($message->getDescription(), 'The authorization did not respond within the alloted time.');
    }    
    public function test_input_card_number_invalid() {
      $message = new SamuraiMessage('message', 'error', 'input.card_number', 'invalid');
      $this->assertEquals($message->getDescription(), 'The card number was invalid.');
    }    
    public function test_input_expiry_month_invalid() {
      $message = new SamuraiMessage('message', 'error', 'input.expiry_month', 'invalid');
      $this->assertEquals($message->getDescription(), 'The expiration date month was invalid, or prior to today.');
    }    
    public function test_input_expiry_year_invalid() {
      $message = new SamuraiMessage('message', 'error', 'input.expiry_year', 'invalid');
      $this->assertEquals($message->getDescription(), 'The expiration date year was invalid, or prior to today.');
    }    
    public function test_input_amount_invalid() {
      $message = new SamuraiMessage('message', 'error', 'input.amount', 'invalid');
      $this->assertEquals($message->getDescription(), 'The transaction amount was invalid.');
    }    
    public function test_processor_transaction_declined_insufficient_funds() {
      $message = new SamuraiMessage('message', 'error', 'processor.transaction', 'declined_insufficient_funds');
      $this->assertEquals($message->getDescription(), 'The transaction was declined due to insufficient funds.');
    }    
  }
?>
