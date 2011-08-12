<?

  class SamuraiException extends Exception {

    private $samurai_messages;

    public function __construct ( $message, $samurai_messages=array() ) {
      parent::__construct( $message );
      $this->samurai_messages = $samurai_messages;
    }

    public function getSamuraiMessages ( ) {
      return $this->samurai_messages;
    }

  }

?>
