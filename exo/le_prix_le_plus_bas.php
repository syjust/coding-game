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

$line_count = array_shift($input);
$product_name = array_shift($input);
$price = null;

foreach ($input as $value) {
    list($p_name, $p_price) = explode(" ", $value);
    if ($p_name == $product_name) {
        if (is_null($price) || $p_price < $price) {
            $price = $p_price;
        }
    }
}
?>
