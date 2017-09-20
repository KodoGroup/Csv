<?php

namespace Ruth\Csv;

class Csv
{
    public static function load($path)
    {
        return CsvReader::new()->load($path);
    }

    public static function __callStatic($method, $args)
    {
        return CsvReader::new()->{$method}(...$args);
    }
}
