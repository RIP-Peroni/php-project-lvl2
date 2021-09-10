<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenereateDiffJson(): void
    {
        $expected = file_get_contents('./tests/fixtures/nestedStylishDiff');
        $actual = genDiff('./tests/fixtures/file1.json', './tests/fixtures/file2.json');
        $this->assertEquals($expected, $actual);
    }

    public function testGenereateDiffYaml(): void
    {
        $expected = file_get_contents('./tests/fixtures/nestedStylishDiff');
        $actual = genDiff('./tests/fixtures/file1.yaml', './tests/fixtures/file2.yml');
        $this->assertEquals($expected, $actual);
    }

    public function testgenDiffNestedPlainJson(): void
    {
        $expected = file_get_contents('./tests/fixtures/nestedPlainDiff');
        $actual = genDiff('./tests/fixtures/file1.json', './tests/fixtures/file2.json', 'plain');
        $this->assertEquals($expected, $actual);
    }

    public function testgenDiffNestedPlainYaml(): void
    {
        $expected = file_get_contents('./tests/fixtures/nestedPlainDiff');
        $actual = genDiff('./tests/fixtures/file1.yaml', './tests/fixtures/file2.yml', 'plain');
        $this->assertEquals($expected, $actual);
    }

    public function testgenDiffNestedJsonJson(): void
    {
        $expected = file_get_contents('./tests/fixtures/nestedJsonDiff');
        $actual = genDiff('./tests/fixtures/file1.json', './tests/fixtures/file2.json', 'json');
        $this->assertEquals($expected, $actual);
    }

    public function testgenDiffNestedJsonYaml(): void
    {
        $expected = file_get_contents('./tests/fixtures/nestedJsonDiff');
        $actual = genDiff('./tests/fixtures/file1.yaml', './tests/fixtures/file2.yml', 'json');
        $this->assertEquals($expected, $actual);
    }
}
