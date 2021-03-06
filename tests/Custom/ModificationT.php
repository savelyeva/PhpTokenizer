<?php

  namespace Test\Funivan\PhpTokenizer\Custom;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/18/13
   */
  class ModificationTest extends \Test\Funivan\PhpTokenizer\MainTestCase {

    public function _testExtractVariableFromString() {

      $lines = array(
        'echo "custom text $f other text";' =>
          'echo "custom text ".$f." other text";',

        'echo "$f end text";' =>
          'echo "".$f." end text";',

        'echo "start text $f";' =>
          'echo "start text ".$f."";',

        'echo "`$f`";' =>
          'echo "`".$f."`";',
      );

      $optimizer = new \Test\Funivan\PhpTokenizer\Demo\ConcatenationOptimizeTest();

      foreach ($lines as $current => $expect) {
        $current = '<?php ' . $current;
        $expect = '<?php ' . $expect;
        $collection = \Funivan\PhpTokenizer\Collection::createFromString($current);

        $optimizer->extractVariableFromString($collection);

        $this->assertEquals($expect, (string) $collection);
      }

    }

    public function _testRemoveEmptyConcatenatedStrings() {

      $lines = array(
        'echo "";' =>
          'echo "";',

        'echo ""."";' =>
          'echo "";',

        'echo "123".\'\';' =>
          'echo "123";',
      );

      $optimizer = new \Test\Funivan\PhpTokenizer\Demo\ConcatenationOptimizeTest();

      foreach ($lines as $current => $expect) {
        $current = '<?php ' . $current;
        $expect = '<?php ' . $expect;
        $collection = \Funivan\PhpTokenizer\Collection::createFromString($current);

        $optimizer->testRemoveEmptyConcatenatedStrings($collection);

        $this->assertEquals($expect, (string) $collection);
      }

    }

    public function _testAllOptimizationMethods() {


      $lines = array(


        'echo "$a";' =>
          'echo $a;',

        'echo "   test$test + $a\"df";' =>
          'echo "   test".$test." + ".$a."\"df";',

        'echo "object $this->table+other";  ' =>
          'echo "object ".$this->table."+other";',

        'echo "$this->table" . "+new";  ' =>
          'echo "$this->table" . "+new";',
        //'echo "{$this->table}" . "+new";  ' => 'echo "{$this->table}" . "+new";',
        //'echo "{$this->table[123]}" . "+new";  ' => 'echo "{$this->table[123]}" . "+new";',
        //'echo "{$item['test']}" . "+new";  '=>'echo "{$item['test']}" . "+new";',
        //'echo "test{$item['test']}" . "+new";  '=>'echo "test{$item['test']}" . "+new";',
      );


      //  '$a;',
      //  '"   test".$test." + ".$a."\"";',
      //  '"object ".$this->table."+other"',
      //  '$this->table."+new"',

      $optimizer = new \Test\Funivan\PhpTokenizer\Demo\ConcatenationOptimizeTest();

      foreach ($lines as $current => $expect) {
        $current = '<?php ' . $current;
        $expect = '<?php ' . $expect;
        $collection = \Funivan\PhpTokenizer\Collection::createFromString($current);

        $i = 10;
        do {

          $changed = 0;
          $changed += (int) $optimizer->extractVariableFromString($collection);
          $changed += (int) $optimizer->extractVariablesFromCurlyBrackets($collection);
          $changed += (int) $optimizer->testRemoveEmptyConcatenatedStrings($collection);
          $i--;

        } while ($changed > 0 and $i > 0);


        $this->assertEquals($expect, (string) $collection);
      }
    }

    public function _testCurlyBrackets() {


      $lines = array(
        //'echo "{$this->table}" . "+new";' =>
        //  'echo $this->table."" . "+new";',

        //'echo "{$this->table[123]}" . "+new";' =>
        //  'echo $this->table[123]."" . "+new";',

        //'echo \'string {$this->table[444]} and \' . "+new";' =>
        //  'echo \'string {$this->table[444]} and \' . "+new";',
        //
        //'echo "{$item[\'test\']}" . "+new";' =>
        //  'echo $item[\'test\']."" . "+new";',
        //
        //'echo "test{$item[\'test\']}" . "+new";' =>
        //  'echo "test".$item[\'test\']."" . "+new";',
        //
        //'echo "test{$item[\'test\']} other string" ;' =>
        //  'echo "test".$item[\'test\']." other string" ;',
        //
        'echo "{$item} other string" ;' =>
          'echo $item." other string" ;',

        ' echo "test \'{$_GET["d"]}\' new";' =>
          ' echo "test \'".$_GET["d"]."\' new";',
      );


      $optimizer = new \Test\Funivan\PhpTokenizer\Demo\ConcatenationOptimizeTest();

      foreach ($lines as $current => $expect) {
        $current = '<?php ' . $current;
        $expect = '<?php ' . $expect;
        $collection = \Funivan\PhpTokenizer\Collection::createFromString($current);

        $optimizer->extractVariablesFromCurlyBrackets($collection);
        $this->assertEquals($expect, (string) $collection);
      }

    }

  }
