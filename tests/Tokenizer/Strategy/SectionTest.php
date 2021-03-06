<?php

  namespace Test\Funivan\PhpTokenizer\Tokenizer\Strategy;

  use Funivan\PhpTokenizer\Collection;
  use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
  use Funivan\PhpTokenizer\Strategy\Section;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 4/18/15
   */
  class SectionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return array
     */
    public function functionCallDataProvider() {
      return array(
        array(
          'header(123); ',
          'header',
          'header(123)',
        ),
        array(
          'echo header          (123, 432); ',
          'header',
          'header          (123, 432)',
        ),

        array(
          'echo header          (123, 432); ',
          'header',
          'header          (123, 432)',
        ),

      );
    }

    /**
     * @dataProvider functionCallDataProvider
     * @param $code
     * @param $functionName
     * @param $expectCode
     */
    public function testFunctionCall($code, $functionName, $expectCode) {
      $code = '<?php ' . $code;

      $collection = Collection::createFromString($code);

      $lines = array();

      foreach ($collection as $index => $token) {
        $q = new QuerySequence($collection, $index);
        $start = $q->strict($functionName);
        $end = $q->section('(', ')');

        if ($q->isValid()) {
          $lines[] = $collection->extractByTokens($start, $end->getLast());
        }
      }

      $this->assertCount(1, $lines);
      $this->assertEquals($expectCode, $lines[0]);
    }

    
    public function testWithEmptySection() {
      $code = '<?php 
      
      header(123);
      
      return;
      
      ';

      $collection = Collection::createFromString($code);
      $linesWithEcho = array();

      foreach ($collection as $index => $token) {
        $q = new QuerySequence($collection, $index);
        $start = $q->strict('return');
        $end = $q->section('(', ')');

        if ($q->isValid()) {
          $linesWithEcho[] = $collection->extractByTokens($start, $end->getLast());
        }
      }

      $this->assertCount(0, $linesWithEcho);

    }

    public function testWithEmptySectionSearch() {
      $code = '<?php 
      
      header(123);
      
      return;
      
      ';

      $collection = Collection::createFromString($code);


      $linesWithEcho = array();

      foreach ($collection as $index => $token) {
        $q = new QuerySequence($collection, $index);
        $start = $q->strict('return');
        $lastToken = $q->process(Section::create()->setDelimiters('(', ')'));

        if ($q->isValid()) {
          $linesWithEcho[] = $collection->extractByTokens($start, $lastToken);
        }
      }

      $this->assertCount(0, $linesWithEcho);

    }

    /**
     * @expectedException \Funivan\PhpTokenizer\Exception\InvalidArgumentException
     */
    public function testInvalidSectionStartDefinition() {

      $section = new Section();
      $section->process(new Collection(), 0);

    }

    /**
     * @expectedException \Funivan\PhpTokenizer\Exception\InvalidArgumentException
     */
    public function testInvalidSectionEndDefinition() {

      $section = new Section();
      $section->setStartQuery(new \Funivan\PhpTokenizer\Query\Query());

      $section->process(new Collection(), 0);

    }

  }
