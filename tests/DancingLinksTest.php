<?php

use PHPUnit\Framework\TestCase;
use MariusLab\DancingLinks;
use MariusLab\SudokuExactCover;

class DancingLinksTest extends TestCase
{
    /** @var \MariusLab\SudokuExactCover $sudokuExactCover */
    private $sudokuExactCover;
    /** @var DancingLinks $dancingLinks */
    private $dancingLinks;

    public function setUp()
    {
        $this->sudokuExactCover = new SudokuExactCover();
        $this->dancingLinks = new DancingLinks();
    }

    public function testGetSolutionSetFromConstraintMatrixList()
    {
        $matrix = [
            [0,0,1,0,0,1,1,0,0],
            [1,0,0,0,1,0,0,1,0],
            [0,1,0,1,0,0,0,0,1],
        ];

        $valueAndPlacement = [
            [1,[0,0]],
            [2,[0,0]],
            [3,[0,0]],
        ];

        try {
            $rootNode = $this->sudokuExactCover->binaryConstraintMatrixToCircularDoublyLinkedList($matrix, $valueAndPlacement);
        } catch (\Exception $e) {

        }

        $solutions = $this->dancingLinks->getSolutionSetFromConstraintMatrixList($rootNode, true, 1);

        $this->assertCount(1, $solutions);
        $this->assertCount(3, array_pop($solutions));

        $matrix = [
            [0,0,1,0,0,1,1,0,0],
            [1,0,0,0,1,0,0,1,0],
            [0,1,0,1,0,0,0,1,1],
        ];

        $valueAndPlacement = [
            [1,[0,0]],
            [2,[0,0]],
            [3,[0,0]],
        ];

        try {
            $rootNode = $this->sudokuExactCover->binaryConstraintMatrixToCircularDoublyLinkedList($matrix, $valueAndPlacement);
        } catch (\Exception $e) {

        }

        $solutions = $this->dancingLinks->getSolutionSetFromConstraintMatrixList($rootNode, true, 1);

        $this->assertNull($solutions);
    }
}
