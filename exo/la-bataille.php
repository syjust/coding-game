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
    public $DEBUG = true;
    public function getStringValue($mixed) {
        $string = "";
        if (is_object($mixed)) {
            if (method_exists($mixed, '__toString')) {
                $string .= "o:$mixed";
            } else {
                $string .= "o:".get_class($mixed);
            }
        } else if (is_array($mixed)) {
            foreach($mixed as $k=>$v) {
                if (empty($string)) {
                    $string .= "a:[$k=>".$this->getStringValue($v);
                } else {
                    $string .= ", $k=>".$this->getStringValue($v);
                }
            }
            $string .= "]";
        } else if (is_null($mixed)) {
            $string = 'n:null';
        } else if ($mixed === false) {
            $string = 'b:false';
        } else if ($mixed === true) {
            $string = 'b:true';
        } else if (empty($mixed)) {
            $string = 'e:empty';
        } else if (is_string($mixed)) {
            $string = "s:'$mixed'";
        } else if (is_int($mixed)) {
            $string = "i:$mixed";
        } else {
            $string = "UNKNOWN:type:".gettype($mixed).",'$mixed'";
        }
        return $string;
    }
    public function debug($mixed) {
        if ($this->DEBUG) {
            $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
            $trace  = isset($traces[1]) ? $traces[1] : [];
            $debug  = get_called_class();
            $debug .= isset($trace['type']) ? $trace['type'] : '.';
            $debug .= isset($trace['function']) ? $trace['function'].'()' : '()';
            $debug .= " : ".$this->getStringValue($mixed);
            error_log($debug);
        }
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
        $this->debug(['player' => $this, 'cards' => $this->cards]);
    }
    public function hasCards() {
        return !empty($this->cards);
    }
}

class Game extends Obj {
    public $LEVELS = [
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

    public $sets       = 0;
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
                $this->cards1[] = array_shift($this->player1->cards);
                $this->cards2[] = array_shift($this->player2->cards);
            }
        }
        return true;
    }

    /**
     * playSet
     *
     * @return boolean true if someone win the set, false otherwise
     * @todo fix 2 fights case (rule seem's misunderstood)
     *
     * @author sylvain.just
     * @date 2018-03-10
     */
    public function playSet() {
        $ret = false;
        $this->sets++;
        $winner = $this->whoWinSet();
        while ($winner != 1 && $winner != 2) {
            if ($winner === 'fight') {
                if ($this->pop(3)) {
                    //$winner = $this->whoWinSet();
                    //if ($winner !== 'fight') {
                        if ($this->pop(1)) {
                            $winner = $this->whoWinSet();
                        }
                    //}
                } else {
                    break;
                }
            } else {
                if ($this->pop(1)) {
                    $winner = $this->whoWinSet();
                } else {
                    break;
                }
            }
        }
        
        if ($winner == 1 || $winner == 2) {
            $ret = true;
            $player = $this->players[$winner];
            $player->winCards($this->cards1);
            $player->winCards($this->cards2);
        }
        $this->debug("set:{$this->sets}, winner:$winner");
        $this->player1->printCards();
        $this->player2->printCards();
        return $ret;
    }
    public function whoWinSet() {
        end($this->cards1);
        end($this->cards2);
        $card1 = current($this->cards1);
        $card2 = current($this->cards2);
        $ret = false;
        if (!empty($card1) && !empty($card2)) {
            if ($this->LEVELS[$card1] > $this->LEVELS[$card2]) {
                $ret = 1;
            } else if ($this->LEVELS[$card1] < $this->LEVELS[$card2]) {
                $ret = 2;
            } else {
                $ret = 'fight';
            }
        }
        $this->debug(['c1' => $card1, 'c2' => $card2, 'ret' => $ret]);
        return $ret;
    }
    public function __toString() {
        if (!empty($this->cards1) || !empty($this->cards2)) {
            return 'PAT';
        } else {
            $winner = 'PAT';
            if ($this->player1->hasCards()) {
                $winner = $this->player1;
            }
            if ($this->player2->hasCards()) {
                $winner = $this->player2;
            }
            if ($winner !== 'PAT') {
                return $winner." ".$this->sets;
            } else {
                return $winner;
            }
        }
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
    if (!$game->playSet()) {
        break;
    }
}

// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$game\n");
?>
