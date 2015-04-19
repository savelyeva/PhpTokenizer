<?php

  namespace Funivan\PhpTokenizer\Strategy;

  use Funivan\PhpTokenizer\Exception\InvalidArgumentException;

  /**
   * Move forward or backward in collection
   *
   * Move forward 12 steps
   * ```
   * $result = (new Move(12))->process($collection, 1);
   * ```                    
   * 
   * Move backward 10 steps
   * ```
   * $result = (new Move(-10))->process($collection, 1);
   * ```
   *
   * @package Funivan\PhpTokenizer\BlockExtractor
   */
  class Move implements StrategyInterface {

    /**
     * Direction forward flag
     */
    const DIRECTION_FORWARD = 1;

    /**
     * Direction back flag
     */
    const DIRECTION_BACK = 2;

    /**
     * @var int
     */
    protected $steps = null;

    /**
     * @var int
     */
    protected $direction = null;

    /**
     * @param int $steps
     * @return static
     */
    public static function create($steps) {
      return new static($steps);
    }

    /**
     * You can pass positive and negative numbers
     * Positive - move forward
     * Negative - move backward
     *
     * @param int $steps
     */
    public function __construct($steps) {

      if (!is_integer($steps)) {
        throw new InvalidArgumentException("Invalid steps. Expect integer. Given: " . gettype($steps));
      }

      $this->steps = $steps;

    }


    /**
     * @inheritdoc
     */
    public function process(\Funivan\PhpTokenizer\Collection $collection, $currentIndex) {
      $result = new Result();

      $endIndex = $currentIndex + $this->steps;

      $result->setNexTokenIndex($endIndex);
      if (isset($collection[$endIndex])) {
        $result->setValid(true);
        $result->setToken($collection[$endIndex]);
      } else {
        $result->setValid(false);
      }

      return $result;
    }

  }