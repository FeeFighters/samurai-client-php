<?

  class SamuraiRequest {

    const SAMURAI_ROOT = 'https://samurai.feefighters.com/v1';

    private $url;
    private $method;
    private $params;

    public function __construct ( $url, $method='GET', $params=array() ) {
      if ( is_null(Samurai::$merchant_key) || is_null(Samurai::$merchant_password) )
        throw new SamuraiException( 'The Samurai API client must be initialized with a merchant key and merchant password' );

      $this->url = self::SAMURAI_ROOT.$url;
      $this->method = $method;
      $this->params = $params;
    }

    public function send ( ) {
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_URL, $this->url );
      curl_setopt( $ch, CURLOPT_USERAGENT, "FeeFighter's Samurai PHP Client v".Samurai::VERSION );
      curl_setopt( $ch, CURLOPT_USERPWD, Samurai::$merchant_key.':'.Samurai::$merchant_password );
      curl_setopt( $ch, CURLOPT_HEADER, FALSE );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
      if ( $this->method != 'GET' ) {
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($this->params) );
      }
      $xml = curl_exec( $ch );

      $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
      if ( $code == 500 )
        throw new SamuraiException( 'Unexpected HTTP code 500 returned from Samurai' );

      return new SamuraiResponse( $xml );
    }

  }

?>
