<?php
error_reporting(E_ALL);
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

/* Vous pouvez aussi effectuer votre traitement ici après avoir lu toutes les données */
$line_count = array_shift($input);
$stations = [];
foreach ($input as $coord) {
  $stations[] = new Station($coord);
}
$i = null;
$j = null;
$dist = 0;
foreach ($stations as $station) {
  if (is_null($i)) {
    $i = $station;
    continue;
  }
  if (!is_null($j)) {
    $i = $j;
  }
  $j = $station;
  $dist += $i->dist($j);
}
// todo: round dist with one of following args
// #PHP_ROUND_HALF_UP
// #PHP_ROUND_HALF_DOWN
// #PHP_ROUND_HALF_EVEN
// #PHP_ROUND_HALF_ODD
echo $dist."\n";

class Station implements Comparable {
  private $x = 0;
  private $y = 0;
  private $z = 0;

  public function __construct($coord) {
    list($this->x, $this->y, $this->z) = explode(" ", $coord);
  }

  public function compareTo($station) {
    if (!$station instanceof Station) {
      throw new Exception("cannot compare something else than a station");
    }
    if ($this->y > $station->y) {
      return 1;
    } else if ($this->y < $station->y) {
      return -1;
    } else {
      return 0;
    }
  }
  // todo: implement dist with following algorithm
  // sqrt(pow(xj-xi, 2)+pow(yj-yi, 2)+pow(zj-zi, 2));
  public function dist($station) {
  }
}
?>
