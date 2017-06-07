<?php

namespace Ruth\Csv;

use Closure;
use League\Csv\Writer;
use SplTempFileObject;

class Csv
{
	private $items;
	private $fields;
	private $csv;

    /**
     * Initialises the Csv builder
     * @param \Illuminate\Support\Collection|array $items
     * @param array $fields
     */
	public function __construct($items, array $fields = [])
	{
		$this->items = $items;
		$this->fields = $fields;
		$this->csv = Writer::createFromFileObject(new SplTempFileObject);
	}

    /**
     * Intansiates the CSV and builds it.
     * @param  \Illuminate\Support\Collection|array $items
     * @param  array  $fields
     * @return \Kodo\Csv\Csv
     */
    public static function build($items, array $fields = [])
    {
    	return (new static($items, $fields))->generate();
    }

    /**
     * Csv writer
     * @return \League\Csv\Writer
     */
    public function csv()
    {
    	return $this->csv;
    }

    /** 
     * Builds the csv data
     * @return \Kodo\Csv\Csv
     */
    public function generate()
    {
    	$headers = [];

        foreach ($this->fields as $key => $value) {
            $headers[] = is_string($key) ? $key : $value;
        }

        $this->csv->insertOne($headers);

        foreach ($this->items as $item) {
            $row = [];

            foreach ($this->fields as $key => $value) {
                $row[] = $this->data_get($item, $value); 
            }

            $this->csv->insertOne($row);
        }

    	return $this;
    }

    /**
     * Saves the csv data as a file
     * @param  string $path
     * @return void
     */
    public function save($path)
    {
        file_put_contents($path, $this->text());
    }

    /** 
     * Creates a download http response
     * @param  string $filename
     * @return void
     */
    public function download($filename)
    {
        $this->csv->output($filename);
    }

    /**
     * Converts the csv file to text
     * @return string
     */
    public function text()
    {
    	return $this->csv->__toString();
    }

    /** 
     * Converts the csv file to array
     * @return array
     */
    public function toArray()
    {
        return $this->csv->jsonSerialize();
    }

    public function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        if ($key instanceof \Closure) {
            return $key($target);
        }

        if ($key != 'meta.value') {
            return;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        var_dump($key, $target);

        while (! is_null($segment = array_shift($key))) {
            // var_dump($segment);
            // if ($segment === '*') {
            //     if (! is_array($target)) {
            //         return ($default instanceof Closure) ? $default() : $default;
            //     }

            //     $result = [];
            //     $key = is_string($key) ? explode('.', $key) : $key;

            //     foreach ($target as $item) {
            //         $result[] = data_get($item, $key);
            //     }

            //     if (in_array('*', $key)) {
            //         $results = [];

            //         foreach ($result as $values) {
            //             if (! is_array($values)) {
            //                 continue;
            //             }

            //             $results = array_merge($results, $values);
            //         }

            //         return $results;
            //     }

            //     return $result;
            // }

            if ((is_array($target) || $target instanceof ArrayAccess) && (($target instanceof ArrayAccess) ? $target->offsetExists($segment) : array_key_exists($segment, $target))) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return ($default instanceof Closure) ? $default() : $default;
            }
        }

        return $target;
    }
}
