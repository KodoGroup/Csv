<?php

namespace Ruth\Csv;

class CsvReader
{
    public function __construct()
    {
        $this->columns = [];
        $this->options = [
            'path'               => null,
            'delimiter'          => ';',
            'headerOffset'       => 0,
            'asObjects'          => false,
            'asAssoc'            => false,
            'trimStrings'        => true,
            'emptyStringsToNull' => true,
        ];
    }

    /**
     * Creates a new instance
     * @return \Ruth\Csv\CsvReader
     */
    public static function new()
    {
        return new static;
    }

    /**
     * Sets the path for the csv file
     * @param  string $path
     * @return \Ruth\Csv\CsvReader
     */
    public function load($path)
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException("File: {$path}");
        }

        $this->options['path'] = $path;

        return $this;
    }

    /** 
     * Override a set of options
     * @param  array $options
     * @return \Ruth\Csv\CsvReader
     */
    public function options($options)
    {
        if (array_key_exists('path', $options)) {
            $this->load($path);
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Set the delimiter
     * @param  string $delimiter
     * @return \Ruth\Csv\CsvReader
     */
    public function delimiter($delimiter)
    {
        $this->options['delimiter'] = $delimiter;

        return $this;
    }

    /**
     * Set the config to auto convert the data to an assoc array
     * @return \Ruth\Csv\CsvReader
     */
    public function asAssoc()
    {
        $this->options['asAssoc'] = true;

        return $this;
    }

    /**
     * Sets the config to convert every line to objects
     * @return \Ruth\Csv\CsvReader
     */
    public function asObjects()
    {
        $this->options['asObjects'] = true;

        return $this;
    }

    /**
     * Sets the column names for every line.
     * @param  array $columns
     * @return \Ruth\Csv\CsvReader
     */
    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Sets which line the header column is placed on.
     * Set false for no header.
     * @param  integer $line
     * @return \Ruth\Csv\CsvReader
     */
    public function headerOffset($line = 0)
    {
        $this->options['headerOffset'] = $line;

        return $this;
    }

    /**
     * Load the data from the csv file into a new array
     * @return array
     */
    private function read()
    {
        $handle = fopen($this->options['path'], "r");

        $lines = [];
        while (($line = fgetcsv($handle, 0, $this->options['delimiter'])) !== false) {
            $lines[] = $line;
        }

        fclose($handle);

        if (array_key_exists(0, $lines) && count($lines[0]) == 1) {
            throw new \Exception("Incorret delimiter \"{$this->options['delimiter']}\"");
        }

        return $lines;
    }

    /**
     * Formats every row based on config
     * @param  array $items
     * @return array
     */
    private function format($items)
    {
        $columnCount = count($this->columns);

        return array_map(function($item) use ($columnCount) {
            $item = array_map(function($value) use ($columnCount) {

                if ($this->options['trimStrings'] === true) {
                    $value = trim($value);
                }

                if ($this->options['emptyStringsToNull'] === true) {
                    $value = is_string($value) && $value === '' ? null : $value;
                }

                return $value;
            }, $item);


            if ($columnCount > 0) {
                if ($columnCount !== count($item)) {
                    throw new MissingColumnException("There there should be {$columnCount} columns, but there is ".count($item)." columns in the row.");
                }
                $item = array_combine($this->columns, $item);;
            }


            if ($this->options['asObjects'] === true) {
                return (object) $item;
            }
            return $item;
        }, $items);
    }

    /** 
     * Executes the read and format of the csv
     * @return \Ruth\Csv\CsvResult
     */
    public function get()
    {
        $lines = $this->read();

        if (($offset = $this->options['headerOffset']) !== false) {
            $columns = array_splice($lines, $offset, 1)[0];

            if (count($this->columns) == 0 || $this->options['asAssoc'] == true) {
                $this->columns = $columns;
            }
        }

        return new CsvResult($this->columns, $this->format($lines));
    }
}