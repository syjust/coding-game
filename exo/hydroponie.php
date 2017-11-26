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
$grid = new Grid($input);
$grid->processVegetables();
print $grid->getCellCountByType("V")."\n";


/**
 * Class: Grid
 *
 *
 * @author sylvain.just
 * @date 2017-11-26
 */
class Grid {
    /**
     * rows
     *
     * @var array of Rows
     */
    public $rows = [];

    public function __construct($rows = array()) {
        foreach ($rows as $rowString) {
            $lastRow = $this->getLastRow();
            $row = $this->addRow($rowString);
            if ($lastRow) {
                foreach($row->cells as $index => $cell) {
                    $lastRow->addNeighbours($index, $cell);
                }
            }
        }
    }

    /**
     * addRow
     *
     * @param string $rowString
     * @return Row added
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addRow($rowString = "") {
        $row = new Row($rowString);
        $this->rows[] = $row;
        return $row;
    }

    /**
     * getLastRow
     *
     * return Row or null
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function getLastRow() {
        $r_cnt = count($this->rows);
        if ($r_cnt > 0) {
            return $this->rows[$r_cnt-1];
        }
        return null;
    }
    public function toString() {
        $string = "";
        foreach($this->rows as $row) {
            foreach($row->cells as $cell) {
                //$string .= $cell->getNeighboursCount();
                $string .= $cell->type;
            }
            $string .= "\n";
        }
        return $string;
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
                        $c->setType("V");
                    }
                }
            }
        }

    }
    public function getCellCountByType($type = "V") {
        $count = 0;
        foreach ($this->rows as $row) {
            foreach ($row->cells as $cell) {
                if ($cell->type == $type) {
                    $count++;
                }
            }
        }
        return $count;
    }
}

/**
 * Class: Row
 *
 *
 * @author sylvain.just
 * @date 2017-11-26
 */
class Row {
    /**
     * cells
     *
     * @var array
     */
    public $cells = array();

    public function __construct($rowString = "") {
        foreach(preg_split("//", $rowString) as $cellType) {
            $lastCell = $this->getLastCell();
            $cell = $this->addCell($cellType);
            if ($lastCell) {
                $lastCell->addNeighbour($cell);
            }
        }
    }

    /**
     * addCell
     *
     * @param string $cellType
     * @return Cell
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addCell($cellType = ".") {
        $cell = new Cell($cellType);
        $this->cells[] = $cell;
        return $cell;
    }

    /**
     * addNeighbours
     *
     * @param int $index
     * @param Cell $cell
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addNeighbours($index = 0, Cell $cell) {
        foreach([$index-1, $index, $index+1] as $idx) {
            if (isset($this->cells[$idx])) {
                $this->cells[$idx]->addNeighbour($cell);
            }
        }
    }
    /**
     * getLastCell
     *
     * return Cell or null
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function getLastCell() {
        $c_cnt = count($this->cells);
        if ($c_cnt > 0) {
            return $this->cells[$c_cnt-1];
        }
        return null;
    }
}

/**
 * Class: Cell
 *
 *
 * @author sylvain.just
 * @date 2017-11-26
 */
class Cell {
    /**
     * type
     *
     * @var string
     */
    public $type = null;

    /**
     * neighbours
     *
     * @var array
     */
    public $neighbours = array();

    /**
     * __construct
     *
     * @param string $type
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function __construct($type) {
        $this->setType($type);
    }

    /**
     * setType
     * possible types are
     * - . (empty)
     * - X (Hydroponie)
     * - V (Vegetable)
     *
     * @param string $type
     * @return boolean (true is set is ok)
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function setType($type = ".") {
        // X can't become V
        if ($this->type == "X" && $type == "V") {
            // do nothing
            return false;
        }
        $this->type = $type;
        return true;
    }
    /**
     * addNeighbour
     *
     * @param Cell $cell
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addNeighbour(Cell $cell) {
        $this->neighbours[] = $cell;
        $cell->neighbours[] = $this;
    }
    /**
     * getNeighboursCount
     *
     * @return int
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function getNeighboursCount() {
        return count($this->neighbours);
    }
}

?>
