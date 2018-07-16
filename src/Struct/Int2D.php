<?php

namespace MariusLab\Struct;

final class Int2D
{
    public $x;
    public $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setX(int $x): Int2D
    {
        $this->x = $x;
        return $this;
    }

    public function setY(int $y): Int2D
    {
        $this->y = $y;
        return $this;
    }

    /**
     * This method assumes that coordinates start from 0
     *
     * @param int $int1D
     * @param int $rowSize
     * @return Int2D
     */
    public static function convert1DCoordTo2D(int $int1D, int $rowSize): Int2D
    {
        $x = (int) $int1D % $rowSize;
        $y = (int) floor($int1D / $rowSize);

        return new Int2D($x, $y);
    }
}
