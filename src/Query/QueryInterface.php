<?php

  namespace Funivan\PhpTokenizer\Query;

  use Funivan\PhpTokenizer;
  use Funivan\PhpTokenizer\Token;

  /**
   *
   * @author Ivan Shcherbak <dev@funivan.com>
   */
  interface QueryInterface {

    /**
     * Check if token is valid for current query
     *
     * @param Token $token
     * @return boolean
     */
    public function isValid(Token $token);

  }