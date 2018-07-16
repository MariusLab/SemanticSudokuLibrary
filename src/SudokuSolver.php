<?php

namespace MariusLab;

class SudokuSolver implements SudokuSolverInterface
{
    private $sudokuExactCover;
    private $dancingLinks;
    /** @var float $lastSudokuSolvedInSeconds */
    private $lastSudokuSolvedInSeconds;

    public function __construct(SudokuExactCover $sudokuExactCover, DancingLinks $dancingLinks)
    {
        $this->sudokuExactCover = $sudokuExactCover;
        $this->dancingLinks = $dancingLinks;
    }

    /**
     * @param array $sudokuGrid[][]
     * @param int $solutionCount = 0
     * @param bool $deterministic = false
     * @return array|null
     * @throws \Exception
     */
    public function solveSudoku(array $sudokuGrid, int $solutionCount = 0, bool $deterministic = false): ?array
    {
        $startTime = microtime(true);
        $matrix = $this->sudokuExactCover->sudokuToBinaryConstraintMatrix($sudokuGrid);
        $valueAndPlacement = $matrix[1];
        $matrix = $matrix[0];
        $rootNode = $this->sudokuExactCover->binaryConstraintMatrixToCircularDoublyLinkedList($matrix, $valueAndPlacement);

        $solutions = $this->dancingLinks->getSolutionSetFromConstraintMatrixList($rootNode, $deterministic, $solutionCount);

        $solvedSudokuPuzzles = [];
        $i = 0;
        foreach ($solutions as $solution) {
            foreach ($solution as $node) {
                $solvedSudokuPuzzles[$i][$node->valueAndPlacement[1][0]][$node->valueAndPlacement[1][1]] = $node->valueAndPlacement[0];
            }
            $i++;
        }

        $endTime = microtime(true);
        $this->lastSudokuSolvedInSeconds =  $endTime-$startTime;
        return $solvedSudokuPuzzles;
    }

    public function getTimeItTookToSolveLastSudokuInSeconds(): float
    {
        return $this->lastSudokuSolvedInSeconds;
    }
}
