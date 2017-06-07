<?php

namespace Ruth\Csv\Tests;

use Ruth\Csv\Csv;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase
{
	public function setUp()
	{
		$this->products =  [
			(object) [
				'title' => 'Demo 1',
				'price' => 10,
				'image' => 'image.jpg',
			],

			(object) [
				'title' => 'Demo 2',
				'price' => 20.2,
				'meta'  => (object)[
					'type' => 'color',
					'value' => 'blue',
				]
			],
		];
	}

	// public function test_creates_an_empty_csv_file()
	// {
	// 	$result = Csv::build([],['title', 'price'])->toArray();

	// 	$this->assertEquals(1, count($result));
	// 	$this->assertEquals(['title', 'price'], $result[0]);
	// }

	// public function test_export_simple_data()
	// {
	// 	$result = Csv::build($this->products , ['title', 'price', 'image'])->toArray();

	// 	$this->assertEquals(3, count($result));
	// 	$this->assertEquals(['title', 'price', 'image'], $result[0]);
	// 	$this->assertEquals(['Demo 1', 10, 'image.jpg'], $result[1]);
	// 	$this->assertEquals(['Demo 2', 20.2, null], $result[2]);
	// }

	// public function test_use_of_a_callback_formatter()
	// {
	// 	$result = Csv::build($this->products , ['sku' => function($item){
	// 		return strtolower(str_replace(' ', '-', $item->title));
	// 	}])->toArray();

	// 	$this->assertEquals(['sku'], $result[0]);
	// 	$this->assertEquals(['demo-1'], $result[1]);
	// 	$this->assertEquals(['demo-2'], $result[2]);
	// }

	public function test_use_of_dot_notation()
	{
		$result = Csv::build($this->products, ['meta.value'])->toArray();
		// var_dump($result);
		$this->assertEquals(['meta.value'], $result[0]);
		$this->assertEquals([null], $result[1]);
		$this->assertEquals(['blue'], $result[2]);
	}
}