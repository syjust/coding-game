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
$adnParser = new AdnParser($input);
do {
  if ($possibleAdn = $indexManager->extractCombination($fragments)) {
    if ($adnParser->match($possibleAdn)) {
      print $adnParser->format($possibleAdn)."\n";
      exit(0);
    }
  }
  $i++;

/**
 * AdnParser
 */
class AdnParser {
  private $part_length = 0;
  private $length = 0;
  /**
   * define size of one adn part
   * & store parts of adn for future formating
   * @param array $input as fragments given
   */
  public function __construct($input) {
    $this->length      = strlen(implode($input));
    $this->part_length = $this->length/2;
  }

  /**
   * verify that an array representing combination (possible adn) is well formated
   * - firstPart & secondPart have the good size
   * - firstPart pattern match secondPart pattern
   * @param array $combination as possibleAdn definition
   */
  public function match($combination) {
    $string = implode($combination);
    if ($this->length == strlen($string)) {
      list($firstPart, $secondPart) = $this->getParts($combination);
      if(strlen($firstPart) == $this->part_length && strlen($secondPart) == $this->part_length) {
        return $this->matchAdn($firstPart, $secondPart);
      }
    }
    return false;
  }

  /**
   * @param string $firstPart
   * @param string $secondPart
   * @return bool
   */
  private function matchAdn($fistPart, $secondPart) {
    foreach(str_split($firstPart) as $idx => $firstAtome) {
      $secondAtome = $secondPart[$idx];
      if (!$this->matchAtome($firstAtome, $secondAtom)) {
        return false;
      }
    }
    return true;
  }
} while ($indexManager->next());

  /**
   * @param char $first
   * @param char $second
   * @return bool
   */
  private function matchAtome($first, $second) {
    switch ($first) {
      case "A" : return ($second == "T");
      case "T" : return ($second == "A");
      case "C" : return ($second == "G");
      case "G" : return ($second == "C");
    }
    return false;
  }

  /**
   * assume combination is well formed adn
   * @param array $combination as possibleAdn definition
   * @return string as formated ADN
   */
  public function format($combination) {
    $fp = [];
    $sp = [];
    $fistPart = "";
    do {
      $part = array_shift($combination);
      $firstPart .= $part;
      $fp[] = $part;
    } while (strlen($firstPart) < $this->part_length);
    $adn = implode($fp)."#".implode($combination);
  }

  /**
   * @param array $combination
   * @return array containing first & second part (2 parts in one array)
   */
  private function getParts($combination) {
    $firstPart = "";
    do {
      $part = array_shift($combination);
      $firstPart .= $part;
    } while (strlen($firstPart) < $this->part_length);
    return [
      $firstPart,
      implode($combination)
    ];
  }
}

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
    $ret = true;
    for ($i = $this->colCount ; $i > 0 ; $i--) {
      /* increment here col by col and reinit when previous col is incremented */
      if ($this->indexes[$i] == $this->maxIndex) {
        if ($i == 0) {
          $ret = false;
          break;
        } else {
          $this->indexes[$i] = 0;
        }
      } else {
        $this->indexes[$i]++;
        $ret = true;
        break;
      }
    }
    return $ret;
  }

  /**
   * @return array as combination in array of array for current index state
   */
  public function extractCombination($arrayOfArray) {
    $combination = [];
    foreach($arrayOfArray as $idx => $array) {
      $combination[] = $array[$this->indexes[$idx]];
    }
    return $combination;
  }

}
?>
