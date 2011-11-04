<?php

  class SamuraiResponse {

    private $response;
    private $messages;

    public function __construct ( $xml ) {
      $root = simplexml_load_string( $xml );
      $root_node = $root->getName();

      if ( $root_node == 'error' )
        $this->handleError( $root );

      list( $this->response, $this->messages ) = $this->parseResponse( $root );
    }

    private function parseResponse ( $root ) {
      $response = array();
      $messages = array();
  
      foreach ( $root->children() as $node ) {

        if ( $node->getName() == 'messages' ) {
          foreach ( $node->children() as $message )
            $messages[] = new SamuraiMessage( (string)$message, (string)$message['subclass'], (string)$message['context'], (string)$message['key'] );
          continue;
        }

        switch ( $node['type'] ) {

          case 'boolean':
            $response[ $node->getName() ] = $node == 'true';
            break;
      
          case 'integer':
            $response[ $node->getName() ] = (integer) $node;
            break;
      
          case 'datetime':
            $response[ $node->getName() ] = new DateTime( $node );
            break;

          case 'nil':
            $response[ $node->getName() ] = null;
            break;

          default:

            $objects = array( 'processor_response', 'payment_method' );
            if ( in_array($node->getName(), $objects) ) {

              list( $r, $m ) = $this->parseResponse( $node );
              $response[ $node->getName() ] = $r;
              $messages = array_merge( $messages, $m );

            } else {

              $response[ $node->getName() ] = (string) $node;

            }
            break;

        }
      }

      return array( $response, $messages );
    }

    public function getField ( $field ) {
      if ( ! array_key_exists($field,$this->response) )
        return false;
      return $this->response[ $field ];
    }

    public function getMessages ( ) {
      return $this->messages;
    }

    public function getSuccess() {
      return $this->getField('success');
    }

    private function handleError ( $root ) {
      $messages = $root->xpath( 'messages/message' );
      $samurai_messages = array();
      foreach ( $messages as $message )
        $samurai_messages[] = new SamuraiMessage( (string)$message, (string)$message['subclass'], (string)$message['context'], (string)$message['key'] );
      throw new SamuraiException( 'An error occurred while processing the Samurai request', $samurai_messages );
    }

  }

?>
