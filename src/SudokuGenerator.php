<?php

namespace MariusLab;

use MariusLab\Struct\Int2D;

class SudokuGenerator implements SudokuGeneratorInterface
{
    const AVAILABLE_CELL_VALUES = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    const BLOCK_WIDTH_IN_CELLS = 3;
    const CELLS_PER_ROW = 9;
    const DIFFICULTY_EASY = 'easy';
    const DIFFICULTY_NORMAL = 'normal';
    const DIFFICULTY_HARD = 'hard';
    const DIFFICULTY_INSANE = 'insane';

    private $sudokuChecker;
    private $sudokuSolver;
    /** @var int $cellsPerRow */
    private $cellsPerRow;
    /** @var int $totalAmountOfCells */
    private $totalAmountOfCells;
    /** @var array $grid[][] */
    private $grid;
    /** @var array $availableGridValues[][] */
    private $availableGridValues;
    /** @var float $lastSudokuGeneratedInSeconds */
    private $lastSudokusGeneratedInSeconds;

    public function __construct(SudokuCheckerInterface $sudokuChecker, SudokuSolverInterface $sudokuSolver)
    {
        $this->sudokuChecker = $sudokuChecker;
        $this->sudokuSolver = $sudokuSolver;
    }

    public function generateSudokuPuzzles(bool $symmetrical = true, int $puzzleCount = 1): array
    {
        $startTime = microtime(true);
        $puzzles = $this->generateSolvedSudokuPuzzles($puzzleCount);
        $sudokuPuzzles = [];
        foreach ($puzzles as &$puzzle) {
            $solution = $puzzle;
            $this->removeCellsFromSolvedSudokuToMakePuzzle($puzzle, $symmetrical);
            $sudokuPuzzles[] = [$puzzle, $solution];
        }

        $endTime = microtime(true);
        $this->lastSudokusGeneratedInSeconds =  $endTime-$startTime;
        return $sudokuPuzzles;
    }

    /**
     * @param array $solvedSudoku
     * @param bool $symmetrical
     * @throws \Exception
     */
    private function removeCellsFromSolvedSudokuToMakePuzzle(array &$solvedSudoku, bool $symmetrical = true): void
    {
        $solutions = [0];
        $lastTwoValues = [];
        while ($solutions !== null && count($solutions) === 1) {
            $randomRow = rand(0, self::CELLS_PER_ROW - 1);
            $randomColumn = rand(0, self::CELLS_PER_ROW - 1);
            if ($symmetrical === true) {
                $randomRow2 = self::CELLS_PER_ROW - 1 - $randomRow;
                $randomColumn2 = self::CELLS_PER_ROW - 1 - $randomColumn;
            } else {
                $randomRow2 = rand(0, self::CELLS_PER_ROW-1);
                $randomColumn2 = rand(0, self::CELLS_PER_ROW-1);
            }

            $lastTwoValues[0] = $solvedSudoku[$randomRow][$randomColumn];
            $lastTwoValues[1] = $solvedSudoku[$randomRow2][$randomColumn2];
            $solvedSudoku[$randomRow][$randomColumn] = null;
            $solvedSudoku[$randomRow2][$randomColumn2] = null;

            $solutions = $this->sudokuSolver->solveSudoku($solvedSudoku, 2, true);
            if ($solutions === null || count($solutions) > 1) {
                $solvedSudoku[$randomRow][$randomColumn] = $lastTwoValues[0];
                $solvedSudoku[8 - $randomRow][8 - $randomColumn] = $lastTwoValues[1];
            }
        }
    }

    /**
     * @param int $puzzleCount
     * @return array
     * @throws \Exception
     */
    private function generateSolvedSudokuPuzzles(int $puzzleCount = 1): array
    {
        return $this->sudokuSolver->solveSudoku(array_fill(0, self::CELLS_PER_ROW, array_fill(0, self::CELLS_PER_ROW, null)), $puzzleCount);
    }

    /**
     * @param int $cellsPerRow
     * @return array
     * @throws \Exception
     */
    public function generateSolvedSudokuPuzzleWithSimpleBacktracking(int $cellsPerRow): array
    {
        $this->checkIfSuppliedCellsPerRowIsValid($cellsPerRow);

        $startTime = microtime(true);
        $this->initializeNew($cellsPerRow);

        $firstRowBucket = self::AVAILABLE_CELL_VALUES;
        shuffle($firstRowBucket);
        for ($current1DPosition = 0; $current1DPosition < $this->totalAmountOfCells; $current1DPosition++) {
            //we start by randomly filling up the first row of the grid to speed up the generation algorithm
            if ($current1DPosition < $this->cellsPerRow) {
                $this->grid[0][$current1DPosition] = array_shift($firstRowBucket);
                continue;
            }
            $current2DPosition = Int2D::convert1DCoordTo2D($current1DPosition, $this->cellsPerRow);

            //check if all available grid values have been exhausted for this cell; and if so - backtrack
            if (count($this->availableGridValues[$current2DPosition->y][$current2DPosition->x]) < 1) {
                $this->resetAvailableGridValuesForCell($current2DPosition);

                //temporarily switch to previous cell
                $current1DPosition--;
                $current2DPosition = Int2D::convert1DCoordTo2D($current1DPosition, $this->cellsPerRow);
                $this->removeValueFromAvailableGridValuesForCell($current2DPosition, $this->grid[$current2DPosition->y][$current2DPosition->x]);

                //backtrack to previous cell on next iteration
                $current1DPosition--;
                continue;
            }

            $randomCellValue = $this->getRandomAvailableGridValueForCell($current2DPosition);
            $this->grid[$current2DPosition->y][$current2DPosition->x] = $randomCellValue;
            $this->removeValueFromAvailableGridValuesForCell($current2DPosition, $randomCellValue);

            if (!$this->sudokuChecker->isColumnValid($this->grid, $current2DPosition->x)
                || !$this->sudokuChecker->isRowValid($this->grid, $current2DPosition->y)
                || !$this->sudokuChecker->is3x3BlockValid($this->grid, $current2DPosition)
            ) {
                $this->grid[$current2DPosition->y][$current2DPosition->x] = null;
                //iterate through current cell again
                $current1DPosition--;
            }
        }

        $endTime = microtime(true);
        $this->lastSudokusGeneratedInSeconds =  $endTime-$startTime;
        return $this->grid;
    }

    public function getTimeItTookToGenerateLastSudokusInSeconds(): float
    {
        return $this->lastSudokusGeneratedInSeconds;
    }

    /**
     * @param int $cellsPerRow
     * @throws \Exception
     */
    protected function checkIfSuppliedCellsPerRowIsValid(int $cellsPerRow): void
    {
        if ($cellsPerRow === 0 || $cellsPerRow % self::BLOCK_WIDTH_IN_CELLS !== 0) {
            throw new \Exception("Cells per row have to be divisable by ".self::BLOCK_WIDTH_IN_CELLS.", so that ".self::BLOCK_WIDTH_IN_CELLS."x".self::BLOCK_WIDTH_IN_CELLS." blocks can be formed.");
        } else if ($cellsPerRow > self::CELLS_PER_ROW) {
            throw new \Exception("Cells per row cannot be more than ".self::CELLS_PER_ROW.".");
        }
    }

    protected function initializeNew(int $cellsPerRow): void
    {
        $this->cellsPerRow = $cellsPerRow;
        $this->totalAmountOfCells = $cellsPerRow * $cellsPerRow;

        $this->grid = array_fill(0, $cellsPerRow, array_fill(0, $cellsPerRow, null));
        $this->availableGridValues = array_fill(0, $cellsPerRow, array_fill(0, $cellsPerRow,self::AVAILABLE_CELL_VALUES));
    }

    protected function getRandomAvailableGridValueForCell(Int2D $int2D): int
    {
        return $this->availableGridValues[$int2D->y][$int2D->x][array_rand($this->availableGridValues[$int2D->y][$int2D->x])];
    }

    protected function resetAvailableGridValuesForCell(Int2D $int2D): void
    {
        $this->availableGridValues[$int2D->y][$int2D->x] = self::AVAILABLE_CELL_VALUES;
    }

    protected function removeValueFromAvailableGridValuesForCell(Int2D $int2D, int $value): void
    {
        $key = array_search($value, $this->availableGridValues[$int2D->y][$int2D->x]);

        if ($key !== false) {
            unset($this->availableGridValues[$int2D->y][$int2D->x][$key]);
        }
    }
}
