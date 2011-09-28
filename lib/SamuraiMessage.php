<?php

  class SamuraiMessage {

    private $message;
    private $class;
    private $context;
    private $key;

    public function __construct ( $message, $class, $context, $key ) {
      $this->message = $message;
      $this->class = $class;
      $this->context = $context;
      $this->key = $key;
    }

    public function getMessage ( ) {
      return $this->message;
    }

    public function getClass ( ) {
      return $this->class;
    }

    public function getContext ( ) {
      return $this->context;
    }

    public function getContextCategory ( ) {
      $parts = explode( '.', $this->context );
      return $parts[0];
    }

    public function getContextType ( ) {
      $parts = explode( '.', $this->context );
      return $parts[1];
    }

    public function getKey ( ) {
      return $this->key;
    }

  }

?>
