<?php

use MariusLab\Struct\Node;
use PHPUnit\Framework\TestCase;
use MariusLab\SudokuExactCover;

class SudokuExactCoverTest extends TestCase
{
    /** @var SudokuExactCover $sudokuExactCover */
    private $sudokuExactCover;

    public function setUp()
    {
        $this->sudokuExactCover = new SudokuExactCover();
    }

    public function testBinaryConstraintMatrixToCircularDoublyLinkedList()
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

        $this->assertNull($rootNode->up);
        $this->assertNull($rootNode->down);
        $this->assertNotNull($rootNode->left);
        $this->assertNotNull($rootNode->right);

        $this->assertEquals(3, $rootNode->left->up->valueAndPlacement[0]);
        $this->assertEquals(2, $rootNode->left->left->up->valueAndPlacement[0]);
        $this->assertEquals(1, $rootNode->left->left->left->up->valueAndPlacement[0]);
        $this->assertEquals(2, $rootNode->right->down->down->down->valueAndPlacement[0]);
        $this->assertEquals(2, $rootNode->right->down->down->down->left->valueAndPlacement[0]);

        $columnCount = 0;
        $expectedColumnCount = count($matrix[0]);
        Node::foreachNodeRight($rootNode, function($iNode) use (&$columnCount) {
                $rowCount = 0;
                $expectedRowCount = 1;
                $this->assertEquals(1, $iNode->nodeCount);
                Node::foreachNodeDown($iNode, function($jNode) use(&$rowCount) {
                    $rowCount++;
                }, true);
                $this->assertEquals($expectedRowCount, $rowCount);

                $columnCount++;
        }, true);
        $this->assertEquals($expectedColumnCount, $columnCount);


        $rowCount = 0;
        $expectedRowCount = 1;
        Node::foreachNodeDown($rootNode->right, function($iNode) use (&$rowCount) {
            $columnCount = 0;
            $expectedColumnCount = 3;
            Node::foreachNodeRight($iNode, function($jNode) use (&$columnCount) {
                $columnCount++;
            });
            $this->assertEquals($expectedColumnCount, $columnCount);

            $rowCount++;
        }, true);
        $this->assertEquals($expectedRowCount, $rowCount);
    }
}
