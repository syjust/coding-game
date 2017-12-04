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

$row_count = array_shift($input);
$a_score = 0;
$b_score = 0;
foreach($input as $round) {
    list($a_round, $b_round) = explode(" ", $round);
    if ($a_round > $b_round) {
        $a_score++;
    } else if ($b_round > $a_round) {
        $b_score++;
    }// else equality
}
if ($a_score > $b_score) {
    echo "A\n";
} else if ($a_score < $b_score) {
    echo "B\n";
} else {
    echo "A = B\n";
}

?>
