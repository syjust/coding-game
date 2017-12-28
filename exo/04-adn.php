<?php
/*******
 * default base for PHP to run in https://demo.isograd.com/runtest/runtest.php
 *
 * Read input from STDIN
 * Use echo or print to output your result, use the PHP_EOL constant at the end of each result line.
 * Use:
 *      local_echo( $variable );
 * to display simple variables in a dedicated area.
 *
 * Use:
 *      local_print_r( $array );
 * to display arrays in a dedicated area.
 * ***/

do{
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if($f!==false){
        $input[] = $f;
    }
}while($f!==false);

// first  : extract line_count (predict we have 8 lines)
$line_count = array_shift($input);

// second : generate 8 fragments array of array
$fragments = [];
for ($i = 0 ; $i < $line_count ; $i++) {
  $fragments[] = $input;
}

// third  : generate all framgents suites with index manager
$indexManager = new IndexManager($line_count);
$possiblesAdn = [];
do {
  if ($possibleAdn = $indexManager->extractCombination($fragments)) {
    $possiblesAdn[] = $possibleAdn;
  }
} while ($indexManager->next());



/**
 * IndexManager will help us to increment arrays indexes
 * from 00000000
 * to   88888888
 */
class IndexManager {

  private $maxIndex;
  private $indexes = [];

  public function __construct($colCount) {
    $this->colCount = $colCount;
    $this->maxIndex = $colCount-1;
    for ($i = 0 ; $i < $colCount ; $i++) {
        $this->indexes[] = 0;
    }
  }

  /**
   * increment indexes
   * @return bool true while there is a next index, false otherwise
   */
  public function next() {
    for ($i = $this->colCount ; $i > 0 ; $i--) {
      /* increment here col by col and reinit when previous col is incremented */
    }
    return false;
  }

  public function extractCombination($arrayOfArray) {
  }

}
?>
