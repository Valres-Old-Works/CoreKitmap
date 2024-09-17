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

namespace Valres\CoreKitmap\managers;

use pocketmine\plugin\PluginOwned;
use pocketmine\utils\SingletonTrait;
use Valres\CoreKitmap\Core;

class ManagerHandler implements PluginOwned
{
    use SingletonTrait;

    /** @var BaseManager[] */
    private array $managers = [];

    public function onDisable(): void {
        foreach($this->managers as $manager){
            $manager->save();
            $this->getOwningPlugin()->getLogger()->info("§4>>§r Manager " . $manager->getName() . " unload avec succès.");
        }
    }

    public function registerManager(BaseManager $manager): void {
        $this->managers[] = $manager;
    }

    public function getOwningPlugin(): Core {
        return Core::getInstance();
    }
}
