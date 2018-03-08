<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/
class Obj {
    function debug($string) {
        error_log(get_called_class()."::$string");
    }
}
class Point extends Obj {
    public $x;
    public $y;
    public function __construct($x, $y) {
        $this->go($x, $y);
    }
    public function go($x, $y) {
        $this->debug("go($x, $y)");
        $this->x = $x;
        $this->y = $y;
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
    public function __construct(Point $fromPoint, Grid $grid) {
        $this->fromPoint = $fromPoint;
        $this->grid = $grid;
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
    public $grid;
    public function __construct($x, $y, Grid $grid) {
        parent::__construct($x, $y);
        $this->grid = $grid;
    }

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
        $crop = new CropController($this, $this->grid);
        foreach(str_split($bombDir) as $direction) {
            switch($direction) {
                case 'U' : $crop->moveUp();    break;
                case 'D' : $crop->moveDown();  break;
                case 'R' : $crop->moveRight(); break;
                case 'L' : $crop->moveLeft();  break;
            }
        }
        $this->grid->copyGrid($crop);
    }
    public function gotoNext($bombDir) {
        $x    = $this->x;
        $y    = $this->y;
        $xSum = 1;
        $ySum = 1;
        if ($this->grid->width > 1) {
            if ($this->grid->width % 2 == 0) {
                $xSum = $this->grid->width / 2;
            } else {
                $xSum = ($this->grid->width + 1) / 2;
            }
        }
        if ($this->grid->height > 1) {
            if ($this->grid->height % 2 == 0) {
                $ySum = $this->grid->height / 2;
            } else {
                $ySum = ($this->grid->height + 1) / 2;
            }
        }
        foreach(str_split($bombDir) as $direction) {
            switch($direction) {
                case 'D' : $y += $ySum; break;
                case 'U' : $y -= $ySum; break;
                case 'R' : $x += $xSum; break;
                case 'L' : $x -= $xSum; break;
            }
        }
        $this->go($x, $y);
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
