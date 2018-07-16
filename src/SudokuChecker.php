<?php

namespace MariusLab;

use MariusLab\Struct\Int2D;

class SudokuChecker implements SudokuCheckerInterface
{
    const AVAILABLE_CELL_VALUES = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    const ALLOWED_CELL_VALUES = [1, 2, 3, 4, 5, 6, 7, 8, 9, null];
    const CELLS_PER_ROW = 9;
    const BLOCK_WIDTH_IN_CELLS = 3;
    const BLOCK_TOTAL_CELLS = 9;
    const ROW = 'row';
    const COLUMN = 'column';
    const IS_VALID = 'isValid';
    const IS_SOLVED = 'isSolved';

    public function isRowValid(array $grid, int $y, bool $verbose = false): bool
    {
        if ($verbose === true) {
            return $this->isRowOrColumnValidOrSolved(self::ROW, self::IS_VALID, $grid, $y);
        } else {
            try {
                return $this->isRowOrColumnValidOrSolved(self::ROW, self::IS_VALID, $grid, $y);
            } catch (\Exception $exception) {
                return false;
            }
        }
    }

    public function isColumnValid(array $grid, int $x, bool $verbose = false): bool
    {
        if ($verbose === true) {
            return $this->isRowOrColumnValidOrSolved(self::COLUMN, self::IS_VALID, $grid, $x);
        } else {
            try {
                return $this->isRowOrColumnValidOrSolved(self::COLUMN, self::IS_VALID, $grid, $x);
            } catch (\Exception $exception) {
                return false;
            }
        }
    }

    public function is3x3BlockValid(array $grid, Int2D $int2D, bool $verbose = false): bool
    {
        if ($verbose === true) {
            return $this->is3x3BlockValidOrSolved(self::IS_VALID, $grid, $int2D);
        } else {
            try {
                return $this->is3x3BlockValidOrSolved(self::IS_VALID, $grid, $int2D);
            } catch (\Exception $exception) {
                return false;
            }
        }
    }

    public function isRowSolved(array $grid, int $y): bool
    {
        return $this->isRowOrColumnValidOrSolved(self::ROW, self::IS_SOLVED, $grid, $y);
    }

    public function isColumnSolved(array $grid, int $x): bool
    {
        return $this->isRowOrColumnValidOrSolved(self::COLUMN, self::IS_SOLVED, $grid, $x);
    }

    public function is3x3BlockSolved(array $grid, Int2D $int2D): bool
    {
        return $this->is3x3BlockValidOrSolved(self::IS_SOLVED, $grid, $int2D);
    }

    public function isSudokuSolved(array $grid): bool
    {
        $this->checkIfSuppliedGridIsOfValidSize($grid);
        $cellsPerRow = $this->getAmountOfCellsPerRowOrColumnInGrid(self::ROW, $grid);
        $totalBlocks = $cellsPerRow*$cellsPerRow/self::BLOCK_TOTAL_CELLS;

        if (!$this->is3x3BlockSolved($grid, new Int2D(0, 0))) {
            return false;
        }

        if ($totalBlocks >= 4) {
            if (!$this->is3x3BlockSolved($grid, new Int2D(3, 0))
                || !$this->is3x3BlockSolved($grid, new Int2D(0, 3))
                || !$this->is3x3BlockSolved($grid, new Int2D(3, 3))) {
                return false;
            }
        }

        if ($totalBlocks === 9) {
            if (!$this->is3x3BlockSolved($grid, new Int2D(6, 0))
                || !$this->is3x3BlockSolved($grid, new Int2D(6, 3))
                || !$this->is3x3BlockSolved($grid, new Int2D(0, 6))
                || !$this->is3x3BlockSolved($grid, new Int2D(3, 6))
                || !$this->is3x3BlockSolved($grid, new Int2D(6, 6))) {
                return false;
            }
        }

        for ($cell = 0; $cell < $cellsPerRow; $cell++) {
            if (!$this->isRowSolved($grid, $cell) || !$this->isColumnSolved($grid, $cell)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array of possible values for a given cell
     *
     * @param array $grid
     * @param Int2D $int2D
     * @return array
     */
    public function getCellCandidates(array $grid, Int2D $int2D): array
    {
        $cellNeighborValues = [];
        for ($y = 0; $y < count($grid); $y++) {
            if ($y === $int2D->y) {
                continue;
            }

            if (!in_array($grid[$y][$int2D->x], $cellNeighborValues, true)) {
                array_push($cellNeighborValues, $grid[$y][$int2D->x]);
            }
        }

        for ($x = 0; $x < count($grid); $x++) {
            if ($x === $int2D->x) {
                continue;
            }

            if (!in_array($grid[$int2D->y][$x], $cellNeighborValues, true)) {
                array_push($cellNeighborValues, $grid[$int2D->y][$x]);
            }
        }

        $xBlock = floor($int2D->x / self::BLOCK_WIDTH_IN_CELLS);
        $yBlock = floor($int2D->y / self::BLOCK_WIDTH_IN_CELLS);

        $xStartPos = (int) $xBlock*self::BLOCK_WIDTH_IN_CELLS;
        $yStartPos = (int) $yBlock*self::BLOCK_WIDTH_IN_CELLS;

        for ($y = $yStartPos; $y < $yStartPos+3; $y++) {
            for ($x = $xStartPos; $x < $xStartPos + 3; $x++) {
                if ($x === $int2D->x && $y === $int2D->y) {
                    continue;
                }

                if (!in_array($grid[$y][$x], $cellNeighborValues, true)) {
                    array_push($cellNeighborValues, $grid[$y][$x]);
                }
            }
        }

        return array_values(array_diff(self::AVAILABLE_CELL_VALUES, $cellNeighborValues));
    }

    /**
     * @param string $rowOrColumn //choose whether to check self::ROW or self::COLUMN
     * @param string $checkIfSolvedOrValid //choose whether to check if self::IS_VALID or self::IS_SOLVED
     * @param array $grid[][]
     * @param int $xOrY //holds the $x or $y coord
     * @return bool
     * @throws \Exception
     */
    protected function isRowOrColumnValidOrSolved(string $rowOrColumn, string $checkIfSolvedOrValid, array $grid, int $xOrY): bool
    {
        $cellsPerRowOrColumn = $this->getAmountOfCellsPerRowOrColumnInGrid($rowOrColumn, $grid, $xOrY);
        if ($checkIfSolvedOrValid === self::IS_VALID && $cellsPerRowOrColumn % self::BLOCK_WIDTH_IN_CELLS !== 0) {
            throw new \Exception("The " . $rowOrColumn . " is not divisible by 3.");
        }
        $cellsValues = [];

        for ($pos = 0; $pos < $cellsPerRowOrColumn; $pos++) {
            if ($rowOrColumn === self::ROW) {
                $x = $pos;
                $y = $xOrY;
            } else {
                $x = $xOrY;
                $y = $pos;
            }
            if (!array_key_exists($y, $grid) || !array_key_exists($x, $grid[$y])) {
                throw new \Exception("Either grid is not properly hydrated or the given x, y coordinates are out of bounds.");
            } else if (!in_array($grid[$y][$x], self::ALLOWED_CELL_VALUES, true)) {
                throw new \Exception("Grid contains invalid values. A valid cell value is 1-9 or null for empty.");
            }

            if ($checkIfSolvedOrValid === self::IS_VALID && $grid[$y][$x] === null) {
                continue;
            }

            if ($grid[$y][$x] === null || in_array($grid[$y][$x], $cellsValues)) {
                return false;
            }
            array_push($cellsValues, $grid[$y][$x]);
        }

        return true;
    }

    /**
     * @param string $checkIfSolvedOrValid //choose whether to check if self::IS_VALID or self::IS_SOLVED
     * @param array $grid[][]
     * @param Int2D $int2D
     * @return bool
     * @throws \Exception
     */
    protected function is3x3BlockValidOrSolved(string $checkIfSolvedOrValid, array $grid, Int2D $int2D): bool
    {
        $cellsValues = [];

        $xBlock = floor($int2D->x / self::BLOCK_WIDTH_IN_CELLS);
        $yBlock = floor($int2D->y / self::BLOCK_WIDTH_IN_CELLS);

        $xStartPos = (int) $xBlock*self::BLOCK_WIDTH_IN_CELLS;
        $yStartPos = (int) $yBlock*self::BLOCK_WIDTH_IN_CELLS;

        for ($y = $yStartPos; $y < $yStartPos+3; $y++) {
            for ($x = $xStartPos; $x < $xStartPos + 3; $x++) {
                if (!array_key_exists($y, $grid) || !array_key_exists($x, $grid[$y])) {
                    throw new \Exception("Either grid is not properly hydrated or the given x, y coordinates are out of bounds.");
                } else if (!in_array($grid[$y][$x], self::ALLOWED_CELL_VALUES, true)) {
                    throw new \Exception("Grid contains invalid values. A valid cell value is 1-9 or null for empty.");
                }

                if ($checkIfSolvedOrValid === self::IS_VALID && $grid[$y][$x] === null) {
                    continue;
                }

                if ($grid[$y][$x] === null || in_array($grid[$y][$x], $cellsValues)) {
                    return false;
                }
                array_push($cellsValues, $grid[$y][$x]);
            }
        }

        return true;
    }

    /**
     * @param array $grid[][]
     * @throws \Exception
     */
    protected function checkIfSuppliedGridIsOfValidSize(array $grid): void
    {
        if (count($grid) === 0) {
            throw new \Exception("SudokuChecker cannot check an empty array.");
        }
        $cellsPerRow = $this->getAmountOfCellsPerRowOrColumnInGrid(self::ROW, $grid);

        if (count($grid) !== count($grid[0])) {
            throw new \Exception("Amount of rows and columns is not identical. Row count has to be equal to column count.");
        }
        else if ($cellsPerRow !== 9) {
            throw new \Exception("SudokuChecker only works with ".self::CELLS_PER_ROW."x".self::CELLS_PER_ROW." grids.");
        } else if ($cellsPerRow > self::CELLS_PER_ROW) {
            throw new \Exception("The supplied grid is larger than ".self::CELLS_PER_ROW."x".self::CELLS_PER_ROW.", which is not supported by SudokuChecker.");
        }
    }

    /**
     * @param string $rowOrColumn
     * @param array $grid
     * @param int $y the reason only y can be passed
     * is because x coord for counting a 2d array is pointless,
     * since it can't be accessed without also inputing an y coord.
     * @return int
     * @throws \Exception
     */
    protected function getAmountOfCellsPerRowOrColumnInGrid(string $rowOrColumn, array $grid, int $y = 0): int
    {
        if ($rowOrColumn == self::COLUMN) {
            return count($grid);
        } else {
            if (!array_key_exists($y, $grid)) {
                throw new \Exception("Tried to access y coordinate out of grid bounds.");
            }
            return count($grid[$y]);
        }
    }
}
