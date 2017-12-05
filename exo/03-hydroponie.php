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
        $rowCount = 0;
        foreach ($rows as $rowString) {
            $rowCount++;
            $lastRow = $this->getLastRow();
            $row = $this->addRow($rowCount, $rowString);
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
    public function addRow($rowCount = 0, $rowString = "") {
        $row = new Row($rowCount, $rowString);
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
    /**
     * toString
     *
     * @param string $format
     *
     * @author sylvain.just
     * @date 2017-12-05
     */
    public function toString($format = 'type') {
        $string = "";
        foreach($this->rows as $row) {
            foreach($row->cells as $cell) {
                $part = "";
                switch($format) {
                    case 'type'   :
                        $part = $cell->type;
                        break;
                    case 'ncount' :
                        $part = $cell->getNeighboursCount();
                        break;
                    case 'a1' :
                        $part = $cell->position;
                        break;
                }
                if (empty($part)) {
                    $part = "E";
                }
                $string .= $part;
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

    /**
     * cols
     *
     * @var array
     */
    private static $cols = [
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z'
    ];
    /**
     * rowCount
     *
     * @var int
     */
    private $rowCount = 0;

    /**
     * __construct
     *
     * @param int $rowCount
     * @param string $rowString
     *
     * @author sylvain.just
     * @date 2017-12-05
     */
    public function __construct($rowCount = 0, $rowString = "") {
        $this->rowCount = $rowCount;
        foreach(str_split($rowString) as $cellType) {
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
        $position = self::$cols[count($this->cells)].$this->rowCount;
        $cell = new Cell($position, $cellType);
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
     * A1 position
     *
     * @var string
     */
    public $position = null;
    /**
     * neighbours
     *
     * @var array
     */
    public $neighbours = array();

    /**
     * __construct
     *
     * @param string $position
     * @param string $type
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function __construct($position, $type) {
        $this->position = $position;
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
