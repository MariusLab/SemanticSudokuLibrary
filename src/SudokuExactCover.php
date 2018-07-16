<?php

namespace MariusLab;

use MariusLab\Struct\Node;

class SudokuExactCover
{
    private $gridCellCount;
    private $gridRowCount;
    private $gridBlockWidth;
    private $constraintSize;

    /**
     * @param array $sudokuGrid
     * @return array
     * @throws \Exception
     */
    public function sudokuToBinaryConstraintMatrix(array $sudokuGrid): array {

        $this->gridCellCount = count($sudokuGrid) * count($sudokuGrid[0]);
        $this->gridRowCount = sqrt($this->gridCellCount);
        $this->gridBlockWidth = sqrt($this->gridRowCount);

        $this->constraintSize = $this->gridRowCount**2;

        if ($this->gridCellCount % $this->gridRowCount != 0) {
            throw new \Exception("The total cell count of the grid has to be a square number.");
        }

        return $this->generateBinaryConstraintMatrixForGrid($sudokuGrid);
    }

    private function generateBinaryConstraintMatrixForGrid(array $grid): array
    {
        $matrixColCount = 4*$this->constraintSize;

        $possiblePlacementPermutations = $binaryConstraintMatrix = [];
        for ($row = 0; $row < $this->gridRowCount; $row++) {
            for ($column = 0; $column < $this->gridRowCount; $column++) {
                for ($value = 1; $value <= $this->gridRowCount; $value++) {
                    if ($grid[$row][$column] === null
                        || $grid[$row][$column] === $value) {
                        $possiblePlacementPermutations[] = [$value, [$row, $column]];
                        $binaryConstraintMatrix[] = array_fill(0, $matrixColCount, 0);
                        $this->satisfyRowConstraints($binaryConstraintMatrix[count($binaryConstraintMatrix)-1], $possiblePlacementPermutations[count($possiblePlacementPermutations)-1]);
                    }
                }
            }
        }

        return [$binaryConstraintMatrix, $possiblePlacementPermutations];
    }

    private function satisfyRowConstraints(array &$row, array $valueAndPlacement): void
    {
        $this->satisfyOneNumberInCell($row, $valueAndPlacement);
        $this->satisfyEachNumberInRow($row, $valueAndPlacement);
        $this->satisfyEachNumberInColumn($row, $valueAndPlacement);
        $this->satisfyEachNumberInBlock($row, $valueAndPlacement);
    }

    private function satisfyOneNumberInCell(array &$row, array $valueAndPlacement): void
    {
        $row[$valueAndPlacement[1][0] * $this->gridRowCount + $valueAndPlacement[1][1]] = 1;
    }

    private function satisfyEachNumberInRow(array &$row, array $valueAndPlacement): void
    {
        $row[$this->constraintSize + (($valueAndPlacement[0] - 1) * $this->gridRowCount + $valueAndPlacement[1][0])] = 1;
    }

    private function satisfyEachNumberInColumn(array &$row, array $valueAndPlacement): void
    {
        $row[$this->constraintSize * 2 + (($valueAndPlacement[0] - 1) * $this->gridRowCount + $valueAndPlacement[1][1])] = 1;
    }

    private function satisfyEachNumberInBlock(array &$row, array $valueAndPlacement): void
    {
        $row[$this->constraintSize * 3 + ($valueAndPlacement[0] - 1) * $this->gridRowCount + (floor($valueAndPlacement[1][0] / $this->gridBlockWidth)) * $this->gridBlockWidth + floor($valueAndPlacement[1][1] / $this->gridBlockWidth)] = 1;
    }

    /**
     * @param array $binaryConstraintMatrix
     * @param array $valueAndPlacement
     * @return Node
     * @throws \Exception
     */
    public function binaryConstraintMatrixToCircularDoublyLinkedList(array $binaryConstraintMatrix, array $valueAndPlacement): Node
    {
        $rootNode = new Node();
        $firstNodeForRow = $lastNodeForRow = [];
        $columnHeader = null;
        for ($column = 0; $column < count(array_values($binaryConstraintMatrix)[0]); $column++) {
            $tempColumnHeader = new Node();
            if ($columnHeader === null) {
                $rootNode->right = $tempColumnHeader;
                $tempColumnHeader->left = $rootNode;
            } else {
                $columnHeader->right = $tempColumnHeader;
                $tempColumnHeader->left = $columnHeader;
            }
            $columnHeader = $tempColumnHeader;

            $node = null;
            $nodeCount = 0;
            for ($row = 0; $row < count($binaryConstraintMatrix); $row++) {
                if ($binaryConstraintMatrix[$row][$column] === 1) {
                    $tempNode = new Node();
                    if ($node === null) {
                        $columnHeader->down = $tempNode;
                        $tempNode->up = $columnHeader;
                    } else {
                        $node->down = $tempNode;
                        $tempNode->up = $node;
                    }
                    $node = $tempNode;
                    $node->columnHeaderNode = $columnHeader;
                    $nodeCount++;

                    $node->valueAndPlacement = $valueAndPlacement[$row];
                    if ($column > 0) {
                        if (isset($lastNodeForRow[$row])) {
                            $lastNodeForRow[$row]->right = $node;
                            $node->left = $lastNodeForRow[$row];
                        }
                    }
                    if (!isset($firstNodeForRow[$row])) {
                        $firstNodeForRow[$row] = $node;
                    }
                    $lastNodeForRow[$row] = $node;
                }
            }

            if ($node === null) {
                throw new \Exception("Provided constraint matrix has unsatisfied constraints.");
            }

            $columnHeader->up = $node;
            $node->down = $columnHeader;
            $columnHeader->nodeCount = $nodeCount;
        }

        foreach ($firstNodeForRow as $key => $node) {
            if (isset($lastNodeForRow[$key])) {
                $node->left = $lastNodeForRow[$key];
                $lastNodeForRow[$key]->right = $node;
            } else {
                $node->left = $node;
                $node->right = $node;
            }
        }

        $columnHeader->right = $rootNode;
        $rootNode->left = $columnHeader;

        return $rootNode;
    }
}
