<?php
$mountains = [
  0 => 0,
  1 => 2,
  2 => 4,
  3 => 3,
  4 => 0,
  5 => 0,
  6 => 0,
  7 => 0
];
asort($mountains);
$rev_mountains = array_reverse($mountains,true);
foreach($rev_mountains as $target => $height) {
  echo("$target ($height)\n"); // The index of the mountain to fire on.
}


?>
