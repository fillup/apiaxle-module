<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   GPLv2+
 */

namespace ApiAxle\Shared;

/**
 * Generic Iteratable Object for storing lists of meetings, attendees, or users.
 * 
 * Class extends \Iterator and adds an addItem($item) method for adding objects
 * to the list.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class ItemList implements \Iterator
{
    private $position = 0;
    private $items = array();
    
    public function __construct() {
        $this->position = 0;
    }
    
    /**
     * Add an object to list
     * 
     * @param ApiAxle\Api|Key|Keyring
     */
    public function addItem($item){
        $this->items[] = $item;
    }
    
    /**
     * Return size of list
     */
    public function size()
    {
        return count($this->items);
    }
    
    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->items[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->items[$this->position]);
    }
}