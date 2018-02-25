<?php
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
        $rowIndex = 0;
        foreach ($rows as $rowString) {
            $rowIndex++;
            $lastRow = $this->getLastRow();
            $row = $this->addRow($rowIndex, $rowString);
            if ($lastRow) {
                foreach($row->cells as $cellIndex => $cell) {
                    $lastRow->addNorthNeighbours($cellIndex, $cell);
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
    public function addRow($rowIndex = 0, $rowString = "") {
        $row = new Row($rowIndex, $rowString);
        $this->rows[$rowIndex] = $row;
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
                        $part = $cell->coordinates;
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
     * getCellCountByType
     *
     * @param mixed $type
     *
     * @author Author sylvain.just
     * @date 2018-02-25
     */
    public function getCellCountByType($type) {
        $count = 0;
        foreach ($this->rows as $row) {
            foreach ($row->cells as $cell) {
                if ($cell->type === $type) {
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
        'a', 'b', 'c', 'd', 'e',
        'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y',
        'z',
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z'
    ];
    /**
     * rowIndex
     *
     * @var int
     */
    private $rowIndex = 0;

    /**
     * __construct
     *
     * @param int $rowIndex
     * @param string $rowString
     *
     * @author sylvain.just
     * @date 2017-12-05
     */
    public function __construct($rowIndex = 0, $rowString = "") {
        $this->rowIndex = $rowIndex;
        foreach(str_split($rowString) as $cellType) {
            $lastCell = $this->getLastCell();
            $cell = $this->addCell($cellType);
            if ($lastCell) {
                $lastCell->addNeighbour('E', $cell);
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
    public function addCell($cellType = "") {
        $absciss       = self::$cols[count($this->cells)];
        $ordinate      = $this->rowIndex;
        $coordinates   = $absciss.$ordinate;
        $cell          = new Cell($coordinates, $cellType);
        $this->cells[$coordinates] = $cell;
        return $cell;
    }

    /**
     * addNorthNeighbours
     *
     * @param int $cellIndex
     * @param Cell $cell
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addNorthNeighbours($cellIndex = 0, Cell $cell) {
        foreach([$cellIndex-1 => 'NE', $cellIndex => 'N', $cellIndex+1 => 'NO'] as $idx => $direction) {
            if (isset($this->cells[$idx])) {
                $this->cells[$idx]->addNeighbour($direction, $cell);
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
     * A1 coordinates
     *
     * @var string
     */
    public $coordinates = null;
    /**
     * neighbours
     *
     * @var array
     */
    public $neighbours = array();
    /**
     * directionsMap
     *
     * @var array as arrayMap
     */
    private static $directionsMap = [
        'NE' => 'SO',
        'N'  => 'S',
        'NO' => 'SE',
        'O'  => 'E',
        'SO' => 'NE',
        'S'  => 'N',
        'SE' => 'NO',
        'E'  => 'O'
    ];

    /**
     * __construct
     *
     * @param string $coordinates
     * @param string $type
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function __construct($coordinates, $type) {
        $this->coordinates = $coordinates;
        $this->setType($type);
    }

    /**
     * setType
     * possible types are
     *
     * @param string $type
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function setType($type) {
        $this->type = $type;
    }
    /**
     * addNeighbour
     *
     * @param string $direction as cardinal direction
     * @param Cell $cell
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addNeighbour($direction, Cell $cell) {
        $this->neighbours[$direction] = $cell;
        $cell->neighbours[self::$directionsMap[$direction]] = $this;
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
