<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 *
 * La structure se présente comme un labyrinthe rectangulaire composé de cellules. Une fois dans le labyrinthe,
 * Kirk peut aller dans les directions suivantes: haut, bas, gauche et droite (UP, DOWN, LEFT, RIGHT).
 *
 *
 * Format du labyrinthe
 * Le labyrinthe est fourni en entrée sous forme ASCII. Le caractère # représente un mur, le caractère.
 * représente un espace vide, la lettre T représente votre position de départ,
 * la lettre C représente la salle de commande
 * et le caractère ? représente une cellule que vous n'avez pas encore scannée.
 *
 **/

/**
 * Class: Obj
 *
 *
 * @author sylvain.just
 * @date 2018-03-10
 */
class Obj {
    public $DEBUG = true;
    public function getTypeString($mixed) {
        $string = "";
        if (is_object($mixed)) {
            $string = "o";
        } else if (is_array($mixed)) {
            $string = "a";
        } else if (is_null($mixed)) {
            $string = 'n';
        } else if ($mixed === false || $mixed === true) {
            $string = 'b';
        } else if (empty($mixed)) {
            $string = 'e';
        } else if (is_string($mixed)) {
            $string = "s";
        } else if (is_int($mixed)) {
            $string = "i$mixed";
        } else {
            $string = gettype($mixed);
        }
        return $string;
    }
    public function getStringValue($mixed, $displayType = false, $displayArrayKeys = false) {
        $string = "";
        $type   = $this->getTypeString($mixed);
        if ($displayType) {
            $string .= "$type:";
        }
        switch($type) {
            case 'o' :
                if (method_exists($mixed, '__toString')) {
                    $string .= $mixed;
                } else {
                    $string .= get_class($mixed);
                }
            break;
            case 'a' :
                $cnt = 0;
                foreach($mixed as $k=>$v) {
                    if (!$cnt) {
                        $string .= "[";
                    } else {
                        $string .= ", ";
                    }
                    if ($displayArrayKeys) {
                        $string .= "$k=>";
                    }
                    $string .= $this->getStringValue($v, $displayType, $displayArrayKeys);
                    $cnt++;
                }
                $string .= "]";
            break;
            case 'n' : $string .= 'NULL' ; break;
            case 'b' : $string .= $mixed ? 'TRUE' : 'FALSE' ; break;
            case 'e' : $string .= 'EMPTY' ; break;
            case 's' : $string .= "'$mixed'" ; break;
            case 'i' : $string .= $mixed ; break;
            default  : $string .= $mixed ; break;
        }
        return $string;
    }
    public function debug($mixed, $displayType = false, $displayArrayKeys = false) {
        if ($this->DEBUG) {
            $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
            $trace  = isset($traces[1]) ? $traces[1] : [];
            $debug  = get_called_class();
            $debug .= isset($trace['type']) ? $trace['type'] : '.';
            $debug .= isset($trace['function']) ? $trace['function'].'()' : '()';
            $debug .= " : ".$this->getStringValue($mixed, $displayType, $displayArrayKeys);
            error_log($debug);
        }
    }
    public function __toString() {
        return get_called_class();
    }
    public function printMe() {
        $this->debug("\n".$this);
    }
}

class Point extends Obj {
    public $x;
    public $y;
    public function __construct($x, $y) {
        //$this->debug("x:$x,y:$y");
        $this->x = $x;
        $this->y = $y;
    }
    public function __toString() {
        return "{$this->x} {$this->y}";
    }

    public function match($x, $y) {
        return $this->x == $x && $this->y == $y;
    }
    public function diff(Point $other) {
        $move = "";
        if ($this->y < $other->y) {
            $move .= "U";
        }
        if ($this->y > $other->y) {
            $move .= "D";
        }
        if ($this->x < $other->x) {
            $move .= "L";
        }
        if ($this->x > $other->x) {
            $move .= "R";
        }
        return $move;
    }
}
class Grid extends Obj {
    public $directions = [
        'L' => 'LEFT',
        'R' => 'RIGHT',
        'U' => 'UP',
        'D' => 'DOWN'
    ];
    public $rows       = [];
    public $startPoint = null;
    public $endPoint   = null;
    public $path       = null;
    public $COLS       = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z'
    ];
    public function __construct(Path $path, $colCount = 0) {
        $title = "";
        for ($i = 0 ; $i < $colCount ; $i++) {
            if (isset($this->COLS[$i])) {
                $title .= $this->COLS[$i];
            } else {
                $title .= $this->COLS[$i%count($this->COLS)];
            }
        }
        $this->debug($title);
        $this->path = $path;
    }
    public function addRow($string) {
        $cnt = count($this->rows);
        if ($cnt < 10) {
            $space = '   ';
        } else if ($cnt < 100) {
            $space = '  ';
        } else if ($cnt < 1000) {
            $space = ' ';
        } else {
            $space = '';
        }
        $this->debug($space.$cnt.":".$string);
        $row = str_split($string);
        if (preg_match('/[CT]/', $string)) {
            for ($i = 0 ; $i < count($row) ; $i++) {
                $dot = $row[$i];
                if ($dot === 'T') {
                    $this->startPoint = new Point(count($this->rows), $i);
                } else if ($dot === 'C') {
                    $this->endPoint = new Point(count($this->rows), $i);
                }
            }
        }
        $this->rows[] = $row;
    }
    public function isEndFound() {
        return !is_null($this->endPoint);
    }
    public function endIsInPath() {
        if ($this->isEndFound()) {
            return $this->path->isInPath($this->endPoint->x, $this->endPoint->y);
        } else {
            return false;
        }
    }
    public function __toString() {
        $string = "";
        foreach ($this->rows as $row) {
            $string .= implode($row)."\n";
        }
        return $string;
    }
    public function canGo($direction, Point $from) {
        $ret  = false;
        switch ($direction) {
            case 'L'    :
                $x = $from->x-1;
                $y = $from->y;
            break;
            case 'R'  :
                $x = $from->x+1;
                $y = $from->y;
            break;
            case 'D'  :
                $x = $from->x;
                $y = $from->y+1;
            break;
            case 'U' :
                $x = $from->x;
                $y = $from->y-1;
            break;
            default      :
                throw new Exception("bad direction '$direction'");
            break;
        }
        if (isset($this->rows[$y]) && isset($this->rows[$y][$x])) {
            $point =  $this->rows[$y][$x];
            $this->debug("$direction?$x,$y:($point)");
            if ($this->endIsInPath()) {
                if (!preg_match("/[#C]/", $point)) {
                    $ret = true;
                }
            } else {
                if (!preg_match("/[#T]/", $point) && !$this->path->isInPath($x, $y)) {
                    $ret = true;
                }
            }
        }
        return $ret;
    }
    public function go($direction) {
        echo "{$this->directions[$direction]}\n";
    }
    public function move($move, Point $from) {
        $this->debug("move:$move");
        $moved = false;
        foreach (str_split($move) as $direction) {
            switch($direction) {
                case 'R' :
                case 'D' :
                case 'U' :
                case 'L' :
                    if ($this->canGo($direction, $from)) {
                        $moved = true;
                        $this->go($direction);
                        break 2;
                    }
                break;
                default      :
                    throw new Exception("bad direction '$direction' in move '$move'");
                break;
            }
        }
        if (!$moved) {
            throw new Exception("can't move '$move' ");
        }
    }
    public function goToEnd(Point $from) {
        $this->debug("from:$from");
        $move = $this->endPoint->diff($from);
        $this->move($move, $from);
    }
    public function returnStart(Point $from) {
        $this->debug("from:$from");
        $move = $this->startPoint->diff($from);
        $this->move($move, $from);
    }
}

class Path extends Obj {
    public $points = [];
    public function addPoint(Point $point) {
        $this->points[] = $point;
    }
    public function __toString() {
        $string = "";
        foreach ($this->points as $point) {
            $string .= "$point";
        }
        return $string;
    }
    public function isInPath($x, $y) {
        $ret = false;
        foreach ($this->points as $point) {
            if ($point->match($x, $y)) {
                $ret = true;
                break;
            }
        }
        return $ret;
    }
}


fscanf(STDIN, "%d %d %d",
    $R, // number of rows.
    $C, // number of columns.
    $A // number of rounds between the time the alarm countdown is activated and the time the alarm goes off.
);
error_log("R:$R, C:$C, A:$A");

// game loop
$path = new Path();
while (TRUE)
{
    fscanf(STDIN, "%d %d",
        $KR, // row where Kirk is located.
        $KC // column where Kirk is located.
    );
    $kirk = new Point($KC, $KR);
    $path->addPoint($kirk);
    $grid = new Grid($path, $C);
    for ($i = 0; $i < $R; $i++)
    {
        fscanf(STDIN, "%s",
            $ROW // C of the characters in '#.TC?' (i.e. one line of the ASCII maze).
        );
        $grid->addRow($ROW);
    }
    //$grid->printMe();

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));

    if ($grid->isEndFound()) {
        if ($grid->endIsInPath()) {
            $grid->goToEnd($kirk);
        } else {
            $grid->returnStart($kirk);
        }
    } else {
        foreach (array_keys($grid->directions) as $direction) {
            if ($grid->canGo($direction, $kirk)) {
                $grid->go($direction);
                break;
            }
        }
    }
    //echo("RIGHT\n"); // Kirk's next move (UP DOWN LEFT or RIGHT).
    //break;
}
?>
