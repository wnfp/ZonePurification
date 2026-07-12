<?php

declare(strict_types=1);

namespace ZonePurification\utils;

use pocketmine\math\Vector3;

class Zone {

    public function __construct(
        private float $x,
        private float $y,
        private float $z,
        private string $worldName
    ) {}

    public function getX(): float {
        return $this->x;
    }

    public function getY(): float {
        return $this->y;
    }

    public function getZ(): float {
        return $this->z;
    }

    public function getWorldName(): string {
        return $this->worldName;
    }

    public function getPosition(): Vector3 {
        return new Vector3($this->x, $this->y, $this->z);
    }
}
