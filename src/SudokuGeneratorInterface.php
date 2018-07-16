<?php

namespace MariusLab;

interface SudokuGeneratorInterface
{
    /**
     * Returns a requested amount of valid sudoku puzzles along with their solutions
     *
     * @return array[][puzzle, solution]
     * @throws \Exception
     */
    function generateSudokuPuzzles(bool $symmetrical, int $puzzleCount): array;
}
