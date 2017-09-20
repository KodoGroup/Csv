<?php

namespace Ruth\Csv;

use Countable;
use ArrayAccess;
use IteratorAggregate;

class CsvResult implements ArrayAccess, Countable, IteratorAggregate
{
    protected $columns = [];
    protected $rows = [];

    public function __construct($columns, $rows)
    {
        $this->columns = $columns;
        $this->rows = $rows;
    }

    /**
     * Fetches the columns
     * @return array
     */
    public function columns()
    {
        return $this->columns;
    }

    /**
     * Converts the result to an array.
     * @return array
     */
    public function toArray()
    {
        return $this->rows;
    }

    /**
     * Adds a new row.
     * @param  integer $key
     * @param  array|object $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->rows[] = $value;
        } else {
            $this->rows[$key] = $value;
        }
    }

    /**
     * Checks if a row exists
     * @param  integer $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->rows);
    }

    /**
     * Removes a row from the list
     * @param  integer $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->rows[$key]);
    }

    /**
     * Select a specefik row
     * @param  integer $key
     * @return array|object
     */
    public function offsetGet($key)
    {
        return $this->rows[$key];
    }

    /**
     * Count the rows
     * @return integer
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Make the object iteratable
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->rows);
    }

    /**
     * Transforms the rows.
     * @param  callable $callback
     * @return \Ruth\Csv\CsvResult
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->rows);

        $items = array_map($callback, $this->rows, $keys);

        return new static($this->columns, array_combine($keys, $items));
    }
}