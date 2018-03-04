<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/
function debug($string) {
    error_log(var_export($string, true));
}
class Batman {
    public $x;
    public $y;
    public function __construct($x, $y) {
        debug("new Batman($x, $y)");
        $this->x = $x;
        $this->y = $y;
    }
    public function __toString() {
        return sprintf("%d %d\n", $this->x, $this->y);
    }
}
class BatGrid {
    public $width  = 0;
    public $height = 0;
    public $x      = 0;
    public $y      = 0;
    public function __construct($width, $height, $x = 0, $y = 0) {
        debug("new BatGrid($width, $height, $x, $y)");
        $this->width = $width;
        $this->height = $height;
        $this->x = $x;
        $this->y = $y;
    }
    /**
     * getGrid
     *
     * @param Batman $batman
     * @param mixed $bombDir
     * @return BatGrid
     *
     * @author sylvain.just
     * @date 2018-02-25
     */
    public function getGrid(Batman $batman, $bombDir) {
        debug("getGrid($batman, $bombDir)");
        $grid = null;
        switch($bombDir) {
            // up 
            case 'U'   :
                $grid = new BatGrid(
                    1,
                    $batman->y,
                    $batman->x,
                    $this->y 
                );
            break;
            // down
            case 'D'   :
                $grid = new BatGrid(
                    1,
                    $this->height-$batman->y,
                    $batman->x,
                    $batman->y+1
                );
            break;
            // right
            case 'R'   :
                $grid = new BatGrid(
                    $this->width-$batman->x,
                    1,
                    $batman->x+1,
                    $batman->y
                );
            break;
            // left
            case 'L'   :
                $grid = new BatGrid(
                    $batman->x,
                    1,
                    $this->x,
                    $batman->y
                );
            break;
            // up-right
            case 'UR'  :
                $grid = new BatGrid(
                    $this->width-$batman->x,
                    $batman->y,
                    $batman->x,
                    $batman->y
                );
            break;
            // down-right
            case 'DR'  :
                $grid = new BatGrid(
                    $this->width-$batman->x,
                    $this->height-$batman->y,
                    $batman->x,
                    $batman->y+1
                );
            break;
            // down-left
            case 'DL'  :
                $grid = new BatGrid(
                    $this->width-$batman->x,
                    $this->height-$batman->y,
                    $batman->x,
                    $batman->y+1
                );
            break;
            // up-left
            case 'UL'  :
                $grid = new BatGrid(
                    $this->width-$batman->x,
                    $this->height-$batman->y,
                    $this->x,
                    $this->y
                );
            break;
        }
        return $grid;
    }
    public function getNextBatman() {
        $x = $this->width > 2
            ? (int)$this->width/2 + $this->x
            : $this->width + $this->x;
        $y = $this->height > 2
            ? (int)$this->height/2 + $this->y
            : $this->height + $this->y;
        return new Batman($x, $y);
    }
}

fscanf(STDIN, "%d %d",
    $W, // width of the building.
    $H // height of the building.
);
$grid = new BatGrid($W, $H);
fscanf(STDIN, "%d",
    $N // maximum number of turns before game over.
);
fscanf(STDIN, "%d %d",
    $X0,
    $Y0
);
$batman = new Batman($X0, $Y0);

// game loop
while (TRUE)
{
    fscanf(STDIN, "%s",
        $bombDir // the direction of the bombs from batman's current location (U, UR, R, DR, D, DL, L or UL)
    );
    $grid = $grid->getGrid($batman, $bombDir);
    $batman = $grid->getNextBatman();

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));


    // the location of the next window Batman should jump to.
    print $batman;
}
?>
