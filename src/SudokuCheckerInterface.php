<?php

namespace MariusLab;

use MariusLab\Struct\Int2D;

interface SudokuCheckerInterface
{
    /**
     * Checks if a specified row is valid based on sudoku rules
     * NOTE: IT DOES NOT CHECK IF THE ROW IS SOLVED
     * This is useful for checking rows that haven't been fully filled in yet
     *
     * @param array $grid[][] //empty cells need to be set to null
     * @param int $y
     * @param bool $verbose set to false by default; doesn't bubble up
     * exceptions; instead returns false if internal exceptions are thrown;
     * Set to true to handle exceptions yourself;
     * @return bool
     * @throws \Exception
     */
    function isRowValid(array $grid, int $y, bool $verbose = false): bool;

    /**
     * Checks if a specified column is valid based on sudoku rules
     * NOTE: IT DOES NOT CHECK IF THE COLUMN IS SOLVED
     * This is useful for checking columns that haven't been fully filled in yet
     *
     * @param array $grid[][] //empty cells need to be set to null
     * @param int $x
     * @param bool $verbose set to false by default; doesn't bubble up
     * exceptions; instead returns false if internal exceptions are thrown;
     * Set to true to handle exceptions yourself;
     * @return bool
     * @throws \Exception
     */
    function isColumnValid(array $grid, int $x, bool $verbose = false): bool;

    /**
     * Checks if a specified 3x3 block is valid based on sudoku rules
     * NOTE: IT DOES NOT CHECK IF THE BLOCK IS SOLVED
     * This is useful for checking blocks that haven't been fully filled in yet
     *
     * @param array $grid[][] //empty cells need to be set to null
     * @param Int2D $int2D
     * @param bool $verbose set to false by default; doesn't bubble up
     * exceptions; instead returns false if internal exceptions are thrown;
     * Set to true to handle exceptions yourself;
     * @return bool
     * @throws \Exception
     */
    function is3x3BlockValid(array $grid, Int2D $int2D, bool $verbose = false): bool;

    /**
     * Checks if row is solved based on sudoku rules, but doesn't check if row is of valid size
     * use isRowValid() for that;
     *
     * @param array $grid[][]
     * @param int $y
     * @return bool
     * @throws \Exception
     */
    function isRowSolved(array $grid, int $y): bool;

    /**
     * Checks if column is solved based on sudoku rules, but doesn't check if column is of valid size
     * use isColumnValid() for that;
     *
     * @param array $grid[][]
     * @param int $x
     * @return bool
     * @throws \Exception
     */
    function isColumnSolved(array $grid, int $x): bool;

    /**
     * Checks if a specified 3x3 block is solved based on sudoku rules
     *
     * @param array $grid[][]
     * @param Int2D $int2D
     * @return bool
     * @throws \Exception
     */
    function is3x3BlockSolved(array $grid, Int2D $int2D): bool;


    /**
     * Checks if the whole sudoku grid is solved based on sudoku rules
     *
     * @param array $grid[][] //grid has to be fully hydrated with empty cells being set to null
     * @return bool
     * @throws \Exception
     */
    function isSudokuSolved(array $grid): bool;
}
