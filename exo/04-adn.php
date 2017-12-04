<?php
/* TODO get input from stdin */
$input = [];

// first  : extract line_count (predict we have 8 lines
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
