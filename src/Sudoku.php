<?php

namespace MariusLab;

class Sudoku
{
    private $sudokuGenerator;
    private $sudokuSolver;

    public function __construct()
    {
        $this->sudokuSolver = new SudokuSolver(new SudokuExactCover(), new DancingLinks());
        $this->sudokuGenerator = new SudokuGenerator(new SudokuChecker(), $this->sudokuSolver);
    }

    /**
     * @param int $puzzleCount
     * @return array
     * @throws \Exception
     */
    public function generateSudokuPuzzles(int $puzzleCount = 1): array
    {
        return $this->sudokuGenerator->generateSudokuPuzzles(true, $puzzleCount);
    }

    public function getTimeItTookToGenerateLastPuzzles(): float
    {
        return $this->sudokuGenerator->getTimeItTookToGenerateLastSudokusInSeconds();
    }

    /**
     * @param array $sudokuGrid
     * @return array|null
     * @throws \Exception
     */
    public function getSolutionToPuzzle(array $sudokuGrid): ?array
    {
        $solutions = $this->sudokuSolver->solveSudoku($sudokuGrid, 1, true);
        if ($solutions === null) {
            return null;
        } else {
            return array_pop($solutions);
        }
    }

    public function getTimeItTookToSolveLastPuzzle(): float
    {
        return $this->sudokuSolver->getTimeItTookToSolveLastSudokuInSeconds();
    }
}
