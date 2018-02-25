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
require_once('Grid.php');
/**
 * Class: VegetableGrid
 *
 * @see Grid
 *
 * @author sylvain.just
 * @date 2018-02-25
 */
class VegetableGrid extends Grid {
    public function __construct($input) {
        parent::__construct('N', $input);
    }
    /**
     * processVegetables
     *
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function processVegetables() {
        foreach ($this->rows as $row) {
            foreach ($row->cells as $cell) {
                if ($cell->type == "X") {
                    foreach ($cell->neighbours as $c) {
                        if ($c->type == '.') {
                            $c->type = "V";
                        }
                    }
                }
            }
        }
    }
}


do{
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if($f!==false){
        $input[] = $f;
    }
}while($f!==false);

$line_count = array_shift($input);
$grid = new VegetableGrid($input);
$grid->processVegetables();
print $grid->getCellCountByType("V")."\n";

?>
