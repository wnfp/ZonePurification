<?php

declare(strict_types=1);

namespace ZonePurification;

use pocketmine\plugin\PluginBase;
use pocketmine\item\StringToItemParser;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use ZonePurification\task\PurificationTask;
use ZonePurification\utils\Zone;

class Main extends PluginBase {

    /** @var Zone[] */
    private array $zones = [];
    private int $radius = 10;
    
    private Item $inputItem;
    private Item $outputItem;
    private int $timeRequired = 30;

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->loadZones();

        $this->getScheduler()->scheduleRepeatingTask(new PurificationTask($this), 20);
    }

    private function loadZones(): void {
        $config = $this->getConfig();
        $this->radius = $config->get("radius", 10);
        $zonesConfig = $config->get("zones", []);

        foreach ($zonesConfig as $zoneString) {
            $parts = explode(":", $zoneString);
            if (count($parts) === 4) {
                $this->zones[] = new Zone(
                    (float)$parts[0],
                    (float)$parts[1],
                    (float)$parts[2],
                    $parts[3]
                );
            }
        }

        $inputString = $config->get("input_item", "minecraft:diamond");
        $outputString = $config->get("output_item", "minecraft:emerald");

        $this->inputItem = StringToItemParser::getInstance()->parse($inputString) ?? VanillaItems::DIAMOND();
        $this->outputItem = StringToItemParser::getInstance()->parse($outputString) ?? VanillaItems::EMERALD();
        
        $this->timeRequired = $config->get("time_required", 30);
    }

    /**
     * @return Zone[]
     */
    public function getZones(): array {
        return $this->zones;
    }

    public function getRadius(): int {
        return $this->radius;
    }

    public function getInputItem(): Item {
        return clone $this->inputItem;
    }

    public function getOutputItem(): Item {
        return clone $this->outputItem;
    }

    public function getTimeRequired(): int {
        return $this->timeRequired;
    }
}
