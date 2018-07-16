<?php

namespace MariusLab;

use MariusLab\Struct\Node;

class DancingLinks
{
    private $solutions;
    private $solution;
    private $rootNode;
    private $requestedSolutionCount;

    public function __construct()
    {
        $this->solutions = [];
        $this->solution = [];
    }

    public function getSolutionSetFromConstraintMatrixList(Node $rootNode, bool $deterministic = true, int $requestedSolutionCount = 0): ?array
    {
        $this->cleanup();
        $this->rootNode = $rootNode;
        $this->requestedSolutionCount = $requestedSolutionCount;
        $this->algorithmX($deterministic);
        return $this->returnSolutions();
    }

    private function returnSolutions(): ?array
    {
        if (count($this->solutions) === 0) {
            return null;
        } else {
            return $this->solutions;
        }
    }

    private function cleanup()
    {
        $this->solutions = $this->solution = [];
    }

    private function algorithmX(bool $deterministic, int $level = 0): void
    {
        if ($this->requestedSolutionCount !== 0 && count($this->solutions) >= $this->requestedSolutionCount) {
            return;
        }
        if ($this->rootNode->right === $this->rootNode) {
            $this->solutions[] = $this->solution;
            return;
        }

        $heuristic = $this->rootNode->right;
        Node::foreachNodeRight($this->rootNode, function ($iNode) use (&$heuristic) {
            if ($iNode->nodeCount < $heuristic->nodeCount) {
                $heuristic = $iNode;
            }
        }, true);

        $this->coverColumn($heuristic);
        if ($deterministic === true) {
            $callFunc = "foreachNodeDown";
        } else {
            $callFunc = "foreachRandomNodeDown";
        }

        Node::$callFunc($heuristic, function (Node $iNode) use ($deterministic, $level) {
            $this->solution[$level] = $iNode;
            Node::foreachNodeRight($iNode, function (Node $jNode) {
                $this->coverColumn($jNode->columnHeaderNode);
            }, true);

            $this->algorithmX($deterministic, $level + 1);

            Node::foreachNodeLeft($iNode, function (Node $jNode) {
                $this->uncoverColumn($jNode->columnHeaderNode);
            }, true);
        }, true);
        $this->uncoverColumn($heuristic);

        return;
    }

    private function coverColumn(Node $columnHeaderNode): void
    {
        $columnHeaderNode->left->right = $columnHeaderNode->right;
        $columnHeaderNode->right->left = $columnHeaderNode->left;
        Node::foreachNodeDown($columnHeaderNode, function(Node $iNode) {
            Node::foreachNodeRight($iNode, function (Node $jNode) {
                $jNode->up->down = $jNode->down;
                $jNode->down->up = $jNode->up;
                $jNode->columnHeaderNode->nodeCount = $jNode->columnHeaderNode->nodeCount - 1;
            }, true);
        }, true);
    }

    private function uncoverColumn(Node $columnHeaderNode): void
    {
        Node::foreachNodeUp($columnHeaderNode, function(Node $iNode) {
            Node::foreachNodeLeft($iNode, function (Node $jNode) {
                $jNode->columnHeaderNode->nodeCount++;
                $jNode->up->down = $jNode;
                $jNode->down->up = $jNode;
            }, true);
        }, true);

        $columnHeaderNode->left->right = $columnHeaderNode;
        $columnHeaderNode->right->left = $columnHeaderNode;
    }
}
