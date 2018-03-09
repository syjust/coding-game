<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/
 
class Obj {
    public function debug($mixed) {
        $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $trace  = isset($traces[1]) ? $traces[1] : [];
        $debug  = get_called_class();
        $debug .= isset($trace['type']) ? $trace['type'] : '.';
        $debug .= isset($trace['function']) ? $trace['function'].'()' : '()';
        if (is_array($mixed)) {
            $debug .= " : [";
            foreach($mixed as $k=>$v) {
                $debug .= "$k=>$v, ";
            }
            $debug .= "]";
        } else {
            $debug .= $mixed;
        }
        error_log($debug);
    }
    public function __toString() {
        return get_called_class();
    }
}
class Player extends Obj {
    public $index;
    public $cards = [];
    public function __construct($index) {
        $this->index = $index;
    }
    public function winCards(array &$cards) {
        while($card = array_pop($cards)) {
            $this->cards[] = $card;
        }
    }
    public function addCard($string) {
        $this->cards[] = preg_replace("/(..?)[DHCS]$/", '$1', $string);
    }
    public function __toString() {
        return (string)$this->index;
    }
    public function printCards() {
        $this->debug($this->cards);
    }
    public function hasCards() {
        return !empty($this->cards);
    }
}

class Game extends Obj {
    public static $LEVELS = [
        '2'  => 2, 
        '3'  => 3, 
        '4'  => 4, 
        '5'  => 5, 
        '6'  => 6, 
        '7'  => 7, 
        '8'  => 8, 
        '9'  => 9, 
        '10' => 10, 
        'J'  => 11, 
        'Q'  => 12, 
        'K'  => 13, 
        'A'  => 14,
    ];

    public $player1;
    public $player2;
    public $players    = [];

    public $cards1     = [];
    public $cards2     = [];

    public $set        = 0;
    public $gameWinner = 'PAT';
    public function __construct(Player $p1, Player $p2) {
        $this->player1 = $p1;
        $this->player2 = $p2;
        $this->players[1] = $p1;
        $this->players[2] = $p2;
    }
    public function pop($cnt) {
        for ($i = 0; $i < $cnt; $i++) {
            if (empty($this->player1->cards) || empty($this->player1->cards)) {
                return false;
            } else {
                $this->cards1 = array_pop($this->player1->cards);
                $this->cards2 = array_pop($this->player2->cards);
            }
        }
        return true;
    }
    public function play() {
        $this->set++;
        $winner = $this->whoWinSet();
        while ($winner != 1 && $winner != 2) {
            if ($winner === 'fight') {
                if ($this->pop(3)) {
                    if ($this->pop(1)) {
                        $winner = $this->whoWinSet();
                    }
                }
            } else {
                if ($this->pop(1)) {
                    $winner = $this->whoWinSet();
                } else {
                    break;
                }
            }
        }
        
        $player = $this->players[$winner];
        $player->winCards($this->cards1);
        $player->winCards($this->cards2);
    }
    public function whoWinSet() {
        end($this->cards1);
        end($this->cards2);
        $card1 = current($this->cards1);
        $card2 = current($this->cards2);
        if (!is_null($card1) && !is_null($card2)) {
            if ($this->LEVELS[$card1] > $this->LEVELS[$card2]) {
                return 1;
            } else if ($this->LEVELS[$card1] < $this->LEVELS[$card2]) {
                return 2;
            } else {
                return 'fight';
            }
        }
        return false;
    }
}

fscanf(STDIN, "%d",
    $n // the number of cards for player 1
);
$p1 = new Player(1);
$p2 = new Player(2);
for ($i = 0; $i < $n; $i++)
{
    fscanf(STDIN, "%s",
        $cardp1 // the n cards of player 1
    );
    $p1->addCard($cardp1);
}
fscanf(STDIN, "%d",
    $m // the number of cards for player 2
);
for ($i = 0; $i < $m; $i++)
{
    fscanf(STDIN, "%s",
        $cardp2 // the m cards of player 2
    );
    $p2->addCard($cardp2);
}
$p1->printCards();
$p2->printCards();
$game = new Game($p1, $p2);

while ($p2->hasCards() && $p1->hasCards()) {
}

// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$game\n");
?>
