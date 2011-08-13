<?

  class SamuraiProcessor {

    private $token;

    public function __construct ( $token ) {
      $this->token = $token;
    }

    public function getToken ( ) {
      return $this->token;
    }
  
  }

?>
