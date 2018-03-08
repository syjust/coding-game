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
    public $grid;
    public function __construct($x, $y, Grid $grid) {
        debug("new Batman($x, $y)");
        $this->go($x, $y);
        $this->grid = $grid;
    }
    public function go($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
    public function __toString() {
        return sprintf("%d %d\n", $this->x, $this->y);
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
        debug("cropGrid($bombDir)");
        switch($bombDir) {
            // up 
            case 'U'   :
                $this->grid->crop(
                    $this->x,
                    $this->grid->y,
                    1,
                    $this->y
                );
            break;
            // down
            case 'D'   :
                $this->grid->crop(
                    $this->x,
                    $this->y+1,
                    1,
                    $this->grid->height-$this->y
                );
            break;
            // right
            case 'R'   :
                $this->grid->crop(
                    $this->x+1,
                    $this->y,
                    $this->grid->width-$this->x,
                    1
                );
            break;
            // left
            case 'L'   :
                $this->grid->crop(
                    $this->grid->x,
                    $this->y,
                    $this->x,
                    1
                );
            break;
            // up-right
            case 'UR'  :
                $this->grid->crop(
                    $this->x,
                    $this->y,
                    $this->grid->width-$this->x,
                    $this->y
                );
            break;
            // down-right
            case 'DR'  :
                $this->grid->crop(
                    $this->x,
                    $this->y+1,
                    $this->grid->width-$this->x,
                    $this->grid->height-$this->y
                );
            break;
            // down-left
            case 'DL'  :
                $this->grid->crop(
                    $this->x,
                    $this->y+1,
                    $this->grid->width-$this->x,
                    $this->grid->height-$this->y
                );
            break;
            // up-left
            case 'UL'  :
                $this->grid->crop(
                    $this->grid->x,
                    $this->grid->y,
                    $this->grid->width-$this->x,
                    $this->grid->height-$this->y
                );
            break;
        }
        return $grid;
    }
    public function gotoNext() {
        $x = $this->grid->width > 2
            ? (int)$this->grid->width/2 + $this->grid->x
            : $this->grid->width + $this->grid->x;
        $y = $this->grid->height > 2
            ? (int)$this->grid->height/2 + $this->grid->y
            : $this->grid->height + $this->grid->y;
        $this->go($x, $y);
    }

}
class BatGrid {
    public $width  = 0;
    public $height = 0;
    public $x      = 0;
    public $y      = 0;
    public function __construct($x, $y, $width, $height) {
        debug("new BatGrid($x, $y, $width, $height)");
        $this->crop($x, $y, $width, $height);
    }
    public function crop($x, $y, $width, $height) {
        $this->width = $width;
        $this->height = $height;
        $this->x = $x;
        $this->y = $y;
    }
}

fscanf(STDIN, "%d %d",
    $W, // width of the building.
    $H // height of the building.
);
$grid = new BatGrid(0, 0, $W, $H);
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
    $batman->cropGrid($bombdir);
    $batman->gotoNext($bombdir);

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));


    // the location of the next window Batman should jump to.
    print $batman;
}
?>
