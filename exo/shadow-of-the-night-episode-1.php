<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/
/**
 * Class: Obj
 *
 *
 * @author sylvain.just
 * @date 2018-03-10
 */
class Obj {
    public $DEBUG = false;
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
}
class Point extends Obj {
    public $x;
    public $y;
    public $grid;
    public function __construct($x, $y, Grid $grid) {
        $this->go($x, $y);
        $this->grid = $grid;
    }

    public function go($x, $y) {
        $this->debug("go($x, $y)");
        $this->x = $x;
        $this->y = $y;
    }
    public function goPoint(Point $point) {
        $this->go($point->x, $point->y);
    }

    public function __toString() {
        return sprintf("%d %d\n", $this->x, $this->y);
    }
}
/**
 * Interface: MoveInterface
 *
 *
 * @author sylvain.just
 * @date 2018-03-08
 */
interface MoveInterface {
    public function moveUp();
    public function moveDown();
    public function moveLeft();
    public function moveRight();
}
/**
 * Class: CropController
 *
 * @see MoveInterface
 * @see Grid
 *
 * @author sylvain.just
 * @date 2018-03-08
 */
class CropController extends Grid implements MoveInterface {
    public $fromPoint;
    public $grid;
    public function __construct(Point $fromPoint) {
        $this->fromPoint = $fromPoint;
        $this->grid = $fromPoint->grid;
        parent::__construct(
            $fromPoint->x, // x:      no left - no right
            $fromPoint->y, // y:      no up - no down
            1,         // width:  no left - no right
            1          // height: no up - no down
        );
    }
    public function moveUp() {
        $this->debug(__FUNCTION__."()");
        $this->y      = $this->grid->y;
        $this->height = $this->fromPoint->y - $this->grid->y;
    }
    public function moveRight() {
        $this->debug(__FUNCTION__."()");
        $this->x      = $this->fromPoint->x + 1;
        $this->width  = ($this->grid->width + $this->grid->x) - ($this->fromPoint->x + 1);
    }
    public function moveDown() {
        $this->debug(__FUNCTION__."()");
        $this->y      = $this->fromPoint->y + 1;
        $this->height = ($this->grid->height + $this->grid->y) - ($this->fromPoint->y + 1);
    }

    public function moveLeft() {
        $this->debug(__FUNCTION__."()");
        $this->x      = $this->grid->x;
        $this->width  = $this->fromPoint->x - $this->grid->x;
    }
}
class Batman extends Point {
    /**
     * cropGrid
     *
     * @param string $bombDir
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function cropGrid($bombDir) {
        $this->debug("cropGrid($bombDir)");
        $crop = new CropController($this);
        $this->move($crop);
        $this->grid->copyGrid($crop);
    }

    public function gotoNext($bombDir) {
        $next = new NextPoint($this);
        $this->move($next, $bombDir);
        $this->goPoint($next);
    }

    public function move(MoveInterface $interface, $bombDir) {
        foreach(str_split($bombDir) as $direction) {
            switch($direction) {
                case 'D' : $interface->moveDown();  break;
                case 'U' : $interface->moveUp();    break;
                case 'R' : $interface->moveRight(); break;
                case 'L' : $interface->moveLeft();  break;
            }
        }
    }

}
class NextPoint extends Point implements MoveInterface {
    public $xSum = 1;
    public $ySum = 1;
    public function __construct(Point $point) {
        $this->goPoint($point);
        if ($this->grid->width > 1) {
            if ($this->grid->width % 2 == 0) {
                $this->xSum = $this->grid->width / 2;
            } else {
                $this->xSum = ($this->grid->width + 1) / 2;
            }
        }
        if ($this->grid->height > 1) {
            if ($this->grid->height % 2 == 0) {
                $this->ySum = $this->grid->height / 2;
            } else {
                $this->ySum = ($this->grid->height + 1) / 2;
            }
        }
    }
    public function moveDown() {
        $this->y += $this->ySum;
    }
    public function moveUp() {
        $this->y -= $this->ySum;
    }
    public function moveRight() {
        $this->x += $this->xSum;
    }
    public function moveLeft() {
        $this->x -= $this->xSum;
    }
}
class Grid extends Obj {
    public $width  = 0;
    public $height = 0;
    public $x      = 0;
    public $y      = 0;
    public function __construct($x, $y, $width, $height) {
        $this->crop($x, $y, $width, $height);
    }
    public function crop($x, $y, $width, $height) {
        $this->debug("crop($x, $y, $width, $height)");
        $this->width = $width;
        $this->height = $height;
        $this->x = $x;
        $this->y = $y;
    }
    public function copyGrid(Grid $grid) {
        $this->crop($grid->x, $grid->y, $grid->width, $grid->height);
    }
}

fscanf(STDIN, "%d %d",
    $W, // width of the building.
    $H // height of the building.
);
$grid = new Grid(0, 0, $W, $H);
fscanf(STDIN, "%d",
    $N // maximum number of turns before game over.
);
fscanf(STDIN, "%d %d",
    $X0,
    $Y0
);
$batman = new Batman($X0, $Y0, $grid);

// game loop
while (TRUE)
{
    fscanf(STDIN, "%s",
        $bombDir // the direction of the bombs from batman's current location (U, UR, R, DR, D, DL, L or UL)
    );
    $batman->cropGrid($bombDir);
    $batman->gotoNext($bombDir);

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));


    // the location of the next window Batman should jump to.
    print $batman;
}
?>
