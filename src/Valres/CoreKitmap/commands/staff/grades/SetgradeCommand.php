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

namespace Valres\CoreKitmap\commands\staff\grades;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\player\CustomPlayer;

class SetgradeCommand extends Command
{
    public function __construct() {
        parent::__construct("setgrade", "Définir le grade du joueur", "usage : /setgrade <player> <grade>");
        $this->setPermission("setgrade.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::GRADES);
        $gradesManager = Core::getInstance()->gradesManager;

        if(count($args) < 2){
            $sender->sendMessage($this->getUsage());
            return;
        }

        [$targetName, $gradeIdentifier] = $args;
        if(!$gradesManager->exist($gradeIdentifier)){
            $sender->sendMessage($config->get("no-grade"));
            return;
        }

        $target = Server::getInstance()->getPlayerExact($targetName);
        if(!$target instanceof CustomPlayer){
            $offlineData = Server::getInstance()->getOfflinePlayerData($targetName);
            if(!$offlineData instanceof CompoundTag){
                $sender->sendMessage($config->get("no-player"));
                return;
            }

            $offlineData->setString("grade", $gradeIdentifier);
            Server::getInstance()->saveOfflinePlayerData($targetName, $offlineData);
            goto sendMessage;
        }

        $target->setGrade($gradesManager->getGrade($gradeIdentifier));

        sendMessage:
        $message = str_replace(
            ["{player}", "{grade}"],
            [$targetName, $gradeIdentifier],
            $config->get("setgrade-message")
        );
        $target->sendMessage($message);
        $sender->sendMessage($message);
    }
}