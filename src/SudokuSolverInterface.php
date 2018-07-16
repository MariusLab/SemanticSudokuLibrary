<?php

namespace MariusLab;

interface SudokuSolverInterface
{
    /**
     * @param array $sudokuGrid[][]
     * @param int $solutionCount = 0; //0 - returns all possible solutions; otherwise returns requested amount of solutions or less;
     * @param bool $deterministic = false //false - returns solutions in a non-predictable order; true - always returns solutions in the same order;
     * @return array|null
     * @throws \Exception
     */
    function solveSudoku(array $sudokuGrid, int $solutionCount = 0, bool $deterministic = false): ?array;
}
