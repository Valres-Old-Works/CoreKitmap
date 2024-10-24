<?php

/**
 *
 *   ____               _  ___ _
 *  / ___|___  _ __ ___| |/ (_) |_ _ __ ___   __ _ _ __
 * | |   / _ \| '__/ _ \ ' /| | __| '_ ` _ \ / _` | '_ \
 * | |__| (_) | | |  __/ . \| | |_| | | | | | (_| | |_) |
 *  \____\___/|_|  \___|_|\_\_|\__|_| |_| |_|\__,_| .__/
 *                                                |_|
 * ENG: This file is strictly confidential and personal.
 * It contains code developed for private purposes and must not be distributed, shared or used without the explicit permission of the author.
 * Any violation will be subject to legal action.
 * FRA: Ce fichier est strictement confidentiel et personnel.
 * Il contient du code développé à des fins privées et ne doit en aucun cas être distribué, partagé ou utilisé sans autorisation explicite de l'auteur.
 * Toute violation sera passible de poursuites légales.
 *
 * @author ValresMC
 * @version v0.0.1
 */

declare(strict_types=1);

namespace Valres\CoreKitmap\tasks;

use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;

class ClearlagTask extends Task
{
    private int $maxTime;
    private int $timer;

    public function __construct(int $maxTime) {
        $this->maxTime = $maxTime;
        $this->timer   = $maxTime;

        Core::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function onRun(): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::CLEARLAG);

        $entities = 0;
        $this->timer--;

        if(in_array($this->timer, $config->get("interval"))){
            Server::getInstance()->broadcastMessage(str_replace("{secondes}", strval($this->timer), $config->get("interval-message")));
        }

        if($this->timer === 0){
            foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
                foreach($world->getEntities() as $entity){
                    if($entity instanceof ItemEntity){
                        $entity->flagForDespawn();
                        $entities++;
                    }
                }
            }
            $this->timer = $this->maxTime;
            Server::getInstance()->broadcastMessage(str_replace("{entity}", strval($entities), $config->get("clearlag-message")));
        }
    }
}