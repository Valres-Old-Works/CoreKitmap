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
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\managers\sanctions\types\Ban;
use Valres\CoreKitmap\managers\sanctions\types\IPBan;
use Valres\CoreKitmap\managers\sanctions\types\UuidBan;

class UnbanallCommand extends Command
{
    public function __construct() {
        parent::__construct("unbanall", "Unban tout les joueurs (sauf les blacklist)", "usage : /unbanall");
        $this->setPermission("unbanall.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::SANCTIONS);

        $sanctionManager = Core::getInstance()->sanctionsManager;
        foreach($sanctionManager->getBans() as $key => $ban){
            match($ban::class){
                Ban::class     => $sanctionManager->removeBan($key),
                IPBan::class   => $sanctionManager->removeIPBan($key),
                UuidBan::class => $sanctionManager->removeUuidBan($key)
            };
        }

        $sender->sendMessage($config->get("unban-all-message"));
    }
}