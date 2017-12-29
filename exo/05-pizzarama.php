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
usort($stations, ['Station', 'compare']);

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
echo ((int)$dist)."\n";

class Station {
  private $x = 0;
  private $y = 0;
  private $z = 0;

  public function __construct($coord) {
    list($this->x, $this->y, $this->z) = explode(" ", $coord);
  }

  public static function compare(Station $first, Station $second) {
    if ($first->y > $second->y) {
      return 1;
    } else if ($first->y < $second->y) {
      return -1;
    } else {
      return 0;
    }
  }
  // todo: implement dist with following algorithm
  // sqrt(pow(xj-xi, 2)+pow(yj-yi, 2)+pow(zj-zi, 2));
  public function dist($station) {
    $x  = $this->x - $station->x;
    $y  = $this->y - $station->y;
    $z  = $this->z - $station->z;
    $px = pow($x, 2);
    $py = pow($y, 2);
    $pz = pow($z, 2);
    $dist = sqrt($px + $py + $pz);
    return $dist;
  }
}
?>
