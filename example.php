<?php

require __DIR__ . '/vendor/autoload.php';

use MariusLab\Sudoku;

$sudoku = new Sudoku();

echo "<div style='display: inline-block;'>";
    try {
        $puzzles = $sudoku->generateSudokuPuzzles(1);
        $puzzle = array_pop($puzzles);
        printSudokuPuzzle($puzzle[0]);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    echo "<br>Sudoku generated in " . $sudoku->getTimeItTookToGenerateLastPuzzles() . "sec<br><br>";
echo "</div>";

echo "<div style='display: inline-block; margin-left: 50px;'>";
    try {
        //NOTE: Solving this puzzle here is just to serve as an example;
        //We already have the solution from the generator which returns a new puzzle and the solution;
        $solution = $sudoku->getSolutionToPuzzle($puzzle[0]);
        printSudokuPuzzle($solution);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    echo "<br>Sudoku solved in " . $sudoku->getTimeItTookToSolveLastPuzzle() . "sec<br><br>";
echo "</div>";

function printSudokuPuzzle(array $sudokuPuzzle): void
{
    $cellsPerRow = count($sudokuPuzzle);

    echo "<table style='border-collapse: collapse;'>";
    echo "<colgroup style='border: solid medium;'><col><col><col>";
    echo "<colgroup style='border: solid medium;'><col><col><col>";
    echo "<colgroup style='border: solid medium;'><col><col><col>";

    for ($y = 0; $y < $cellsPerRow; $y++) {
        if ($y % 3 === 0) {
            echo "<tbody style='border: solid medium;'>";
        }
        echo "<tr>";
        for ($x = 0; $x < $cellsPerRow; $x++) {
            echo "<td style='border: solid thin; height: 2em; width: 2em; text-align: center; padding: 0;'>";
            if ($sudokuPuzzle[$y][$x] !== null) {
                echo $sudokuPuzzle[$y][$x];
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
