<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\generateDiff;

class DifferTest extends TestCase
{
	public function testGenereateDiff(): void
	{
		$expected = file_get_contents('./tests/fixtures/plainStylishDiff');
		$actual = generateDiff('./tests/fixtures/file1.json', './tests/fixtures/file2.json');
		$this->assertEquals($expected, $actual);
	}
}