<?php

namespace MariusLab\Struct;

final class Node
{
    /** @var null|Node */
    public $left;
    /** @var null|Node */
    public $right;
    /** @var null|Node */
    public $up;
    /** @var null|Node */
    public $down;
    /** @var null|Node */
    public $columnHeaderNode;
    /** @var array[$value, [$row, $column]] */
    public $valueAndPlacement;
    /** @var null|int */
    public $nodeCount;

    public function __construct()
    {
        $this->left = null;
        $this->right = null;
        $this->up = null;
        $this->down = null;
        $this->columnHeaderNode = null;
        $this->valueAndPlacement = null;
        $this->nodeCount = null;
    }

    static public function foreachNodeRight(Node $node, $callback, $skipFirstElement = false): void
    {
        if ($skipFirstElement === false) {
            $iNode = $node;
        } else {
            $iNode = $node->right;
        }
        do {
            call_user_func($callback, $iNode);
            $iNode = $iNode->right;
        } while($iNode !== $node);
    }

    static public function foreachNodeLeft(Node $node, $callback, $skipFirstElement = false): void
    {
        if ($skipFirstElement === false) {
            $iNode = $node;
        } else {
            $iNode = $node->left;
        }
        do {
            call_user_func($callback, $iNode);
            $iNode = $iNode->left;
        } while($iNode !== $node);
    }

    static public function foreachNodeDown(Node $node, $callback, $skipFirstElement = false): void
    {
        if ($skipFirstElement === false) {
            $iNode = $node;
        } else {
            if ($node === $node->down) {
                return;
            }
            $iNode = $node->down;
        }

        do {
            call_user_func($callback, $iNode);
            $iNode = $iNode->down;
        } while($iNode !== $node);
    }

    static public function foreachRandomNodeDown(Node $node, $callback, $skipFirstElement = false): void
    {
        $nodes = [];
        if ($skipFirstElement === false) {
            $iNode = $node;
        } else {
            if ($node === $node->down) {
                return;
            }
            $iNode = $node->down;
        }

        do {
            $nodes[] = $iNode;
            $iNode = $iNode->down;
        } while($iNode !== $node);

        while (count($nodes) > 0) {
            $randomNodeKey = array_rand($nodes);
            call_user_func($callback, $nodes[$randomNodeKey]);
            unset($nodes[$randomNodeKey]);
        }
    }


    static public function foreachNodeUp(Node $node, $callback, $skipFirstElement = false): void
    {
        if ($skipFirstElement === false) {
            $iNode = $node;
        } else {
            if ($node === $node->up) {
                return;
            }
            $iNode = $node->up;
        }

        do {
            call_user_func($callback, $iNode);
            $iNode = $iNode->up;
        } while($iNode !== $node);
    }
}
