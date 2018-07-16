<?php

use PHPUnit\Framework\TestCase;
use MariusLab\SudokuChecker;
use MariusLab\Struct\Int2D;

class SudokuCheckerTest extends TestCase
{
    /** @var SudokuChecker $sudokuChecker */
    private $sudokuChecker;

    public function setUp()
    {
        $this->sudokuChecker = new SudokuChecker();
    }

    /**
     * @throws Exception
     */
    public function testIsSudokuSolved()
    {
        $this->assertTrue($this->sudokuChecker->isSudokuSolved([
            [6, 1, 8, 4, 3, 2, 7, 5, 9],
            [2, 7, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ]));

        $this->assertFalse($this->sudokuChecker->isSudokuSolved([
            [6, 1, 8, 4, 3, 2, 7, 5, 9],
            [2, 7, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 1, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, null, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ]));
    }

    /**
     * @throws Exception
     */
    public function testIs3x3BlockValidOrSolved()
    {
        $this->assertTrue($this->sudokuChecker->is3x3BlockValid([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ], new Int2D(3, 3)));

        $this->assertFalse($this->sudokuChecker->is3x3BlockSolved([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 6, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ], new Int2D(3, 3)));

        $this->assertTrue($this->sudokuChecker->is3x3BlockSolved([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ], new Int2D(3, 3)));

        $this->assertFalse($this->sudokuChecker->is3x3BlockValid([
            [6, 1, 8, 4, 3, 2, 7, 5, 9],
            [2, 7, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 6, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ], new Int2D(3, 3)));
    }

    /**
     * @throws Exception
     */
    public function testIsRowValidOrSolved()
    {
        $this->assertTrue($this->sudokuChecker->isRowValid([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, null, null, null, 8, 2, 1],
        ], 8));

        $this->assertTrue($this->sudokuChecker->isRowSolved([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ], 8));

        $this->assertFalse($this->sudokuChecker->isRowValid([
            [2, 4, 6],
        ], 1));

        $this->expectException(\Exception::class);
        $this->assertTrue($this->sudokuChecker->isRowSolved([
            [2, 4, 6],
        ], 1));
    }

    /**
     * @throws Exception
     */
    public function testIsColumnValidOrSolved()
    {
        $this->assertTrue($this->sudokuChecker->isColumnValid([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, null, null, null, 8, 2, 1],
        ], 8));

        $this->assertTrue($this->sudokuChecker->isColumnSolved([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
            [7, 2, 9, 3, 8, 1, 6, 4, 5],
            [1, 8, 7, 2, 4, 6, 5, 9, 3],
            [3, 5, 2, 9, 1, 8, 4, 6, 7],
            [9, 4, 6, 5, 7, 3, 8, 2, 1],
        ], 8));

        $this->expectException(\Exception::class);
        $this->sudokuChecker->isColumnValid([
            [6, 6, 6, 4, 3, 2, 7, 5, 9],
            [2, 6, 4, 8, 9, 5, 1, 3, 6],
            [5, 9, 3, 1, 6, 7, 2, 8, 4],
            [8, 3, 1, 6, 5, 4, 9, 7, 2],
            [4, 6, 5, 7, 2, 9, 3, 1, 8],
        ], 8, true);
    }
}
