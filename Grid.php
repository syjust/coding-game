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

    /**
     * cells
     *
     * @var array of Cells indexed by xy notation
     */
    public $cells = [];

    /**
     * __construct
     *
     * @param string $ordonateDirection as N or S
     * @param array $rows of string
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function __construct($ordonateDirection, $rows = array()) {
        if (!in_array($ordonateDirection, ['N', 'S'])) {
            throw new Exception("'$ordonateDirection' : bad ordonate Direction, please give me 'N' or 'S'.)");
        }
        foreach ($rows as $rowString) {
            $lastRow = $this->getLastRow();
            $row = $this->addRow($rowString);
            if ($lastRow) {
                foreach($row->cells as $cellIndex => $cell) {
                    $lastRow->addRowNeigbours($ordonateDirection, $cellIndex, $cell);
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
    public function addRow($rowString) {
        $rowIndex = count($this->rows);
        $row = new Row($this, $rowIndex, $rowString);
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
     * getCell
     *
     * @param string $coordinates as xy notation
     * @return Cell or null
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function getCell($coordinates) {
        if (isset($this->cells[$coordinates])) {
            return $this->cells[$coordinates];
        }
        return null;
    }

    /**
     * toString
     * print grid as given type
     *
     * @param string $format as :
     *   - type   for cell type
     *   - ncount for neighbours count
     *   - a1     for cell coordinates
     *   - xy     for cell coordinates
     * @return string
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
                        $part = $cell->getCoordinates('a1');
                        break;
                    case 'xy' :
                        $part = $cell->getCoordinates('xy');
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
     * __toString
     *
     * @return string
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function __toString() {
        return $this->toString('type');
    }

    /**
     * getCellCountByType
     *
     * @param string $type
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
     * grid
     *
     * @var Grid
     */
    public $grid;

    /**
     * cells
     *
     * @var array
     */
    public $cells = array();

    /**
     * rowIndex
     *
     * @var int
     */
    public $rowIndex;

    /**
     * __construct
     *
     * @param Grid $grid
     * @param int $rowIndex
     * @param string $rowString
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function __construct(Grid $grid, $rowIndex, $rowString) {
        $this->grid = $grid;
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
    public function addCell($cellType) {
        $x             = count($this->cells);
        $y             = $this->rowIndex;
        $coordinates   = "$x $y";
        $cell          = new Cell($this, $x, $y, $cellType);
        $this->grid->cells[$coordinates] = $cell;
        $this->cells[] = $cell;
        return $cell;
    }

    /**
     * addRowNeigbours
     *
     * @param int $cellIndex
     * @param Cell $cell
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function addRowNeigbours($direction, $cellIndex = 0, Cell $cell) {
        $directionsMapIndex = [
            $cellIndex-1 => $direction.'E',
            $cellIndex => $direction,
            $cellIndex+1 => $direction.'O'
        ];
        foreach($directionsMapIndex as $idx => $direction) {
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
     * a1AbscissMap
     *
     * @var array
     */
    private static $a1AbscissMap = [
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z'
    ];

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
     * row
     *
     * @var Row
     */
    public $row;
    /**
     * type
     *
     * @var string
     */
    public $type = null;

    /**
     * x absciss index
     *
     * @var int
     */
    public $x;

    /**
     * y ordinate index
     *
     * @var int
     */
    public $y;

    /**
     * neighbours
     *
     * @var array
     */
    public $neighbours = array();

    /**
     * __construct
     *
     * @param Row $row
     * @param int $x
     * @param int $y
     * @param string $type
     *
     * @author sylvain.just
     * @date 2017-11-26
     */
    public function __construct(Row $row, $x, $y, $type) {
        $this->row  = $row;
        $this->x    = $x;
        $this->y    = $y;
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

    /**
     * getNeighbour
     *
     * @param string $direction
     * @return Cell or null
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function getNeighbour($direction) {
        if (isset($this->neighbours[$direction])) {
            return $this->neighbours[$direction];
        }
        return null;
    }

    /**
     * getCoordinates
     *
     * @param string $coordinatesType as a1 or xy
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function getCoordinates($coordinatesType) {
        $coordinates = null;
        switch($coordinatesType) {
            case 'a1' :
                if (isset(self::$a1AbscissMap[$this->x])) {
                    $coordinates = self::$a1AbscissMap[$this->x]."{$this->y}";
                } else {
                    throw new Exception(sprintf(
                        "'%s' : absciss index exceed a1 absciss map count (%d > %d)",
                        $coordinatesType, $this->x, count(self::$a1AbscissMap)
                    ));
                }
                break;
            case 'xy' : $coordinates = "{$this->x} {$this->y}" ; break;
            default : throw new Exception("'$coordinatesType' : bad coordinates type");
        }
        return $coordinates;
    }
}
?>
