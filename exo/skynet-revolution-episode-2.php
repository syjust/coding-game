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
$network->computeHotNodes();

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
    /**
     * isHotNode
     *
     * @var boolean (true if more than one hotLink)
     */
    public $isHotNode = false;

    /**
     * __construct
     *
     * @param int $index
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function __construct(int $index) {
        $this->index = $index;
    }

    /**
     * __toString
     *
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function __toString() {
        return strval($this->index);
    }

    /**
     * countHotLinks
     *
     * @return int as count hotLinks
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function countHotLinks() {
        $count = 0;
        foreach($this->links as $link) {
            if ($link->isHotLink && !$link->isBroken) {
                $count++;
            }
        }
        return $count;
    }
}
class Link {
    public $nodes    = [];
    public $isBroken = false;
    /**
     * isHotLink
     *
     * @var boolean (true if have a node gateway)
     */
    public $isHotLink  = false;
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
    public function other(Node $node) {
        foreach($this->nodes as $other) {
            if ($node->index != $other->index) {
                return $other;
            }
        }
        return null;
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
    /**
     * map hot nodes by node index
     */
    public $hotNodes = [];

    /**
     * addLink
     *
     * @param int $indexA
     * @param int $indexB
     *
     * @author sylvain.just
     * @date 2018-03-02
     */
    public function addLink(int $indexA, int $indexB) {
        $nodeA = $this->addNodeByIndex($indexA);
        $nodeB = $this->addNodeByIndex($indexB);
        $link = new Link($nodeA, $nodeB);
    }

    /**
     * addNodeByIndex
     *
     * @param int $index
     * @param boolean $hot
     *
     * @author sylvain.just
     * @date 2018-03-02
     */
    public function addNodeByIndex(int $index, $hot = false) {
        $node = $this->hasNode($index) ? $this->getNode($index) : new Node($index);
        if ($hot) {
        } else {
            $this->addNode($node);
        }
        return $node;
    }

    /**
     * addNode
     *
     * @param Node $node
     * @param boolean $hot
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function addNode(Node $node, $hot = false) {
        if (!$this->hasNode($node->index, $hot)) {
            if ($hot) {
                $this->hotNodes[$node->index] = $node;
            } else {
                $this->nodes[$node->index] = $node;
            }
        }
    }

    /**
     * hasNode
     *
     * @param int $index
     * @param boolean $hot
     * @return boolean
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function hasNode(int $index, $hot = false) {
        return $hot ? isset($this->hotNodes[$index]) : isset($this->nodes[$index]);
    }

    /**
     * getNode
     *
     * @param int $index
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function getNode(int $index) {
        return $this->nodes[$index];
    }

    /**
     * setGateway
     *
     * @param int $index
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function setGateway(int $index) {
        error_log("setting gateway index '$index'");
        if ($this->hasNode($index)) {
            $node = $this->getNode($index);
            $node->isGateway = true;
            $this->gateways[$index] = $node;
            foreach($node->links as $link) {
                $link->isHotLink = true;
            }
        }
    }

    /**
     * countMinLinkBetween
     *
     * consider rule of skynet agent : go to the near gateway
     *
     * @param int $skynetIndex
     * @param int $hotNodeIndex
     * @return int as min link count between 2 nodes
     *
     * @todo pathBuilder implementation
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function countMinLinkBetween(int $skynetIndex, int $hotNodeIndex) {
        $path = [];
        $skynetNode = $this->getNode($skynetIndex);
        foreach($skynetNode->links as $link) {
            if ($link->hasGateway()) {
                $path[] = $link->other($skynetNode);
            }
        }
    }

    /**
     * computeHotNodes
     *
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function computeHotNodes() {
        foreach($this->nodes as $node) {
            $this->computeHotNode($node);
        }
    }

    public function computeHotNode(Node $node) {
        if ($node->countHotLinks() >= 2) {
            $node->isHotNode = true;
            $this->hotNodes[$node->index] = $node;
        } else {
            if (isset($this->hodeNodes[$node->index])) {
                unset($this->hodeNodes[$node->index]);
            }
            $node->isHotNode = false;
        }
    }

    /**
     * destroyLinkNear
     *
     * @param int $skynetIndex
     *
     * @author sylvain.just
     * @date 2018-03-03
     */
    public function destroyLinkNear(int $skynetIndex) {
        if ($this->hasNode($skynetIndex)) {
            $link = null;
            // tryDestroy link between current skynetIndex and a one step gateway
            error_log("tryDestroy link between current skynetIndex ($skynetIndex) and a one step gateway");
            $linkDestroyed = false;
            
            // 1. destroy direct link with gateway and current skynetIndex
            foreach($this->getNode($skynetIndex)->links as $link) {
                if ($link->tryDestroy()) {
                    $linkDestroyed = true;
                    break;
                }
            }
            
            // 2. destroy first hotlink (2 gateways for 1 node) on the skynet logic way (SN always go on the closer path to gateway)
            // todo: implement this (finalize pathBuilder in countMinLinkBetween())
            if (!$linkDestroyed) {
                error_log("gateway link not found from current skynetIndex ($skynetIndex) looping on hotNodes");
                $hotNode = null;
                foreach($this->hotNodes as $node) {
                    if (is_null($hotNode)) {
                        $hotNode = $node;
                        continue;
                    }
                    if ($this->countMinLinkBetween($skynetIndex, $node->index) < $this->countMinLinkBetween($skynetIndex, $hotNode->index)) {
                        $hotNode = $node;
                    }
                }
                if (!is_null($hotNode)) {
                    foreach($hotNode->links as $link) {
                        if ($link->tryDestroy()) {
                            $linkDestroyed = true;
                            break;
                        }
                    }
                }
            }

            // 3. destroy a link to gateway somewhere in the network
            if (!$linkDestroyed) {
                // gateway link not found from current skynetIndex
                error_log("gateway link not found from hotNodes near skynetIndex ($skynetIndex) looping randomly on gateways");
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
