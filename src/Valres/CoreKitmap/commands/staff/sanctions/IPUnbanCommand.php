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

namespace Valres\CoreKitmap\commands\staff\sanctions;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;

class IPUnbanCommand extends Command
{
    public function __construct() {
        parent::__construct("ip-unban", "Unban IP un joueur", "usage : /ip-unban <player>");
        $this->setPermission("ip-unban.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::SANCTIONS);
        if(count($args) < 1){
            $sender->sendMessage($this->getUsage());
            return;
        }

        $playerName = $args[0];

        $altManager = Core::getInstance()->accountManager;
        if(!$altManager->exist($playerName)){
            $sender->sendMessage($config->get("no-players"));
            return;
        }

        $sanctionManager = Core::getInstance()->sanctionsManager;
        $IPs = $altManager->getIPs($playerName);
        foreach($IPs as $IP){
            if($sanctionManager->isIPBanned($IP)){
                $sanctionManager->removeIPBan($IP);
            }
        }

        $sender->sendMessage(str_replace(
            "{player}",
            $playerName,
            $config->get("ip-unban-message")
        ));
        Server::getInstance()->broadcastMessage(str_replace(
            ["{player}", "{author}"],
            [$playerName, $sender->getName()],
            $config->get("unban-message")
        ));
    }
}
