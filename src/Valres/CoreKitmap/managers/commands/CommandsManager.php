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

namespace Valres\CoreKitmap\managers\commands;

use pocketmine\command\Command;
use pocketmine\Server;
use Valres\CoreKitmap\managers\BaseManager;
use Valres\CoreKitmap\utils\Utils;

class CommandsManager extends BaseManager
{

    public function getName(): string {
        return "Commands";
    }

    public function load(): void {
        $this->deleteCommands();

        Utils::callDirectory("commands", function(string $namespace): void {
            Server::getInstance()->getCommandMap()->register("kitmap-core", new $namespace);
        });
    }

    public function deleteCommands(): void {
        $commands = ["ban", "ban-ip", "unban", "unban-ip", "banlist"];
        foreach($commands as $commandName){
            $command = Server::getInstance()->getCommandMap()->getCommand($commandName);
            if($command instanceof Command){
                Server::getInstance()->getCommandMap()->unregister($command);
            }
        }
    }

    public function save(): void {}
}
