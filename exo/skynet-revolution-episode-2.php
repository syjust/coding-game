<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d %d %d",
    $N, // the total number of nodes in the level, including the gateways
    $L, // the number of links
    $E // the number of exit gateways
);
$network = new Network();
for ($i = 0; $i < $L; $i++)
{
    fscanf(STDIN, "%d %d",
        $N1, // N1 and N2 defines a link between these nodes
        $N2
    );
    $network->addLink($N1, $N2);
}
for ($i = 0; $i < $E; $i++)
{
    fscanf(STDIN, "%d",
        $EI // the index of a gateway node
    );
    $network->setGateway($EI);
}

// game loop
while (TRUE)
{
    fscanf(STDIN, "%d",
        $SI // The index of the node on which the Skynet agent is positioned this turn
    );

    $network->destroyLinkNear($SI);
    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));


    // Example: 0 1 are the indices of the nodes you wish to sever the link between
}
class Node {
    public $index     = null;
    public $isGateway = false;
    public $links     = [];
    public function __construct(int $index) {
        $this->index = $index;
    }
    public function __toString() {
        return strval($this->index);
    }
}
class Link {
    public $nodes    = [];
    public $isBroken = false;
    public function __construct($nodeA, $nodeB) {
        $this->nodes[$nodeA->index] = $nodeA;
        $this->nodes[$nodeB->index] = $nodeB;
        $nodeA->links[] = $this;
        $nodeB->links[] = $this;
    }
    public function hasGateway() {
        foreach ($this->nodes as $node) {
            if ($node->isGateway) {
                return true;
            }
        }
        return false;
    }
    public function __toString() {
        return implode(" ", $this->nodes);
    }
    public function tryDestroy() {
        $ret = false;
        error_log("trying link '$this'");
        if ($this->hasGateway()) {
            if (!$this->isBroken) {
                $this->isBroken = true;
                print "$this\n";
                error_log("'$this' DESTROYED");
                $ret = true;
            } else {
                error_log("'$this' already destroyed");
            }
        } else {
            error_log("'$this' no gateway found");
        }
        return $ret;
    }
}
class Network {
    /**
     * map nodes by node index
     */
    public $nodes    = [];
    /**
     * map linkArray by node index
     */
    public $gateways = [];
    public function addLink(int $indexA, int $indexB) {
        $nodeA = $this->hasNode($indexA) ? $this->getNode($indexA) : new Node($indexA);
        $nodeB = $this->hasNode($indexB) ? $this->getNode($indexB) : new Node($indexB);
        $this->addNode($nodeA);
        $this->addNode($nodeB);
        $link = new Link($nodeA, $nodeB);
    }
    public function addNode(Node $node) {
        if (!$this->hasNode($node->index)) {
            $this->nodes[$node->index] = $node;
        }
    }
    public function hasNode(int $index) {
        return isset($this->nodes[$index]);
    }
    public function getNode(int $index) {
        return $this->nodes[$index];
    }
    public function setGateway(int $index) {
        error_log("setting gateway index '$index'");
        if ($this->hasNode($index)) {
            $node = $this->getNode($index);
            $node->isGateway = true;
            $this->gateways[$index] = $node;
        }
    }
    public function destroyLinkNear(int $index) {
        if ($this->hasNode($index)) {
            $link = null;
            // tryDestroy link between current index and a one step gateway
            error_log("tryDestroy link between current index ($index) and a one step gateway");
            $linkDestroyed = false;
            
            // 1. destroy direct link with gateway and current index
            foreach($this->getNode($index)->links as $link) {
                if ($link->tryDestroy()) {
                    $linkDestroyed = true;
                    break;
                }
            }
            
            // 2. destroy first hotlink (2 gateways for 1 node) on the skynet logic way (SN always go on the closer path to gateway)
            // TODO: implement this
            
            // 3. destroy a link to gateway somewhere in the network
            if (!$linkDestroyed) {
                // gateway link not found from current index
                error_log("gateway link not found from current index ($index) looping on gateways");
                foreach($this->gateways as $node) {
                    foreach ($node->links as $link) {
                        if ($link->tryDestroy()) {
                            break 2;
                        }
                    }
                }
            }
        }
    }
}
?>
