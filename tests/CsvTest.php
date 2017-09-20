<?php

namespace Ruth\Csv\Tests;

use PHPUnit\Framework\TestCase;
use Ruth\Csv\Csv;
use Ruth\Csv\CsvReader;

class CsvTest extends TestCase
{
	public function setUp()
	{
		$this->file = __DIR__.'/files/foobar.csv';
	}

	/**
     * @expectedException \Ruth\Csv\FileNotFoundException
     */
    public function test_file_not_found()
    {
        CsvReader::new()->load(__DIR__.'/files/missing.csv');
    }

    public function test_can_change_delimiter()
    {
    	$csv = CsvReader::new()->delimiter('Foo');

    	$this->assertEquals('Foo', $csv->options['delimiter']);
    }

    public function test_can_change_row_type_to_assoc()
    {
    	$csv = CsvReader::new()->load($this->file)->asAssoc();

    	$this->assertTrue($csv->options['asAssoc']);
    }

    public function test_can_change_row_type_to_objects()
    {
    	$csv = CsvReader::new()->load($this->file)->asObjects();

    	$this->assertTrue($csv->options['asObjects']);
    	$this->assertTrue(is_object($csv->get()[0]));
    }

    public function test_can_trim_rows()
    {
    	$csv = CsvReader::new()->load($this->file);

    	$this->assertTrue($csv->options['trimStrings']);
    	$this->assertEquals('I got spaces', $csv->get()[3][1])
    }
}