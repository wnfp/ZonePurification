<?php

declare(strict_types=1);

namespace ZonePurification\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use ZonePurification\Main;

class PurificationTask extends Task {

    /** @var array<string, int> */
    private array $playerTimes = [];

    public function __construct(
        private Main $plugin
    ) {}

    public function onRun(): void {
        $server = Server::getInstance();
        $radiusSquared = $this->plugin->getRadius() ** 2;
        
        $inputItem = $this->plugin->getInputItem();
        $outputItem = $this->plugin->getOutputItem();
        $requiredTime = $this->plugin->getTimeRequired();

        $playersInZone = [];

        foreach ($this->plugin->getZones() as $zone) {
            $world = $server->getWorldManager()->getWorldByName($zone->getWorldName());
            if ($world === null) {
                continue;
            }

            $center = $zone->getPosition();
            
            foreach ($world->getPlayers() as $player) {
                if ($player->getPosition()->distanceSquared($center) <= $radiusSquared) {
                    $playersInZone[$player->getName()] = $player;
                }
            }
        }

        foreach ($server->getOnlinePlayers() as $player) {
            $name = $player->getName();

            if (isset($playersInZone[$name])) {
                $inventory = $player->getInventory();

                if ($inventory->contains($inputItem)) {
                    $this->playerTimes[$name] = ($this->playerTimes[$name] ?? 0) + 1;
                    
                    $remainingTime = $requiredTime - $this->playerTimes[$name];

                    if ($this->playerTimes[$name] >= $requiredTime) {
                        $this->playerTimes[$name] = 0;

                        $inventory->removeItem($inputItem->setCount(1));
                        $inventory->addItem($outputItem->setCount(1));
                        
                        $player->sendMessage("§a- §fYou have finished purifying your item");
                        $player->sendActionBarMessage(" ");
                    } else {
                        $player->sendActionBarMessage("§a" . $remainingTime . "s");
                    }
                } else {
                    if (isset($this->playerTimes[$name])) {
                        $player->sendActionBarMessage(" ");
                        unset($this->playerTimes[$name]);
                    }
                }
            } else {
                if (isset($this->playerTimes[$name])) {
                    $player->sendActionBarMessage(" ");
                    unset($this->playerTimes[$name]);
                }
            }
        }
    }
}
