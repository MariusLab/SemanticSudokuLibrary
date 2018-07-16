<?php

use \PHPUnit\Framework\TestCase;
use MariusLab\SudokuChecker;
use MariusLab\SudokuSolver;
use MariusLab\SudokuExactCover;
use MariusLab\DancingLinks;
use MariusLab\SudokuGenerator;

class SudokuGeneratorTest extends TestCase
{
    /** @var SudokuChecker $sudokuChecker */
    private $sudokuChecker;
    /** @var SudokuGenerator $sudokuGenerator */
    private $sudokuGenerator;

    public function setUp()
    {
        //we will rely on SudokuChecker to test our generator, so it is important to fully test SudokuChecker first
        $this->sudokuChecker = new SudokuChecker();
        //the solver is really the same as generator, it only acts as a wrapper class for exact cover and dancing links classes, so test those
        $sudokuSolver = new SudokuSolver(new SudokuExactCover(), new DancingLinks());
        $this->sudokuGenerator = new SudokuGenerator($this->sudokuChecker, $sudokuSolver);
    }

    /**
     * @throws Exception
     */
    public function testGenerateSudokuPuzzles()
    {
        $puzzles = $this->sudokuGenerator->generateSudokuPuzzles(true, 2);
        $this->assertCount(2, $puzzles);
        $this->assertArrayHasKey(0, $puzzles[0]);
        $this->assertArrayHasKey(1, $puzzles[0]);
        $this->assertCount(9, $puzzles[0][0]);
        $this->assertCount(9, $puzzles[1][0]);

        foreach($puzzles as $puzzle) {
            foreach ($puzzle[1] as $solvedPuzzle) {
                foreach ($solvedPuzzle as $cell) {
                    $this->assertNotNull($cell);
                }
            }
        }

        $this->assertFalse($this->sudokuChecker->isSudokuSolved($puzzles[0][0]));
        $this->assertTrue($this->sudokuChecker->isSudokuSolved($puzzles[0][1]));
        $this->assertInternalType('float', $this->sudokuGenerator->getTimeItTookToGenerateLastSudokusInSeconds());
    }
}
