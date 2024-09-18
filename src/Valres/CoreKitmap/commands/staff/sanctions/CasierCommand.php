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
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\managers\sanctions\CasierJudiciaire;
use Valres\CoreKitmap\player\CustomPlayer;
use Valres\CoreKitmap\utils\TimeHelper;

class CasierCommand extends Command
{
    public function __construct() {
        parent::__construct("casier", "Voir le casier d'un joueur", "usage : /casier <player>");
        $this->setPermission("casier.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::SANCTIONS);
        if(count($args) < 1){
            $sender->sendMessage($this->getUsage());
            return;
        }

        $target = $args[0];

        if(!$target instanceof CustomPlayer){
            $offlineData = Server::getInstance()->getOfflinePlayerData($target);
            if(!$offlineData instanceof CompoundTag){
                $sender->sendMessage($config->get("no-players"));
                return;
            }
            /** @var CasierJudiciaire $casier */
            $casier = unserialize($offlineData->getString("casier", serialize(new CasierJudiciaire())));
        } else $casier = $target->getCasierJudiciaire();

        $message = str_replace("{player}", (($target instanceof Player) ? $target->getName() : $target), $config->get("casier-title")) . "\n";
        $message .= str_replace("{bans}", strval(count($casier->getBans())), $config->get("casier-ban-title")) . "\n";
        foreach($casier->getBans() as $ban){
            $message .= str_replace(
                ["{date}", "{reason}", "{author}"],
                [TimeHelper::timestampToDate($ban->getTime()), $ban->getReason(), $ban->getAuthorName()],
                $config->get("casier-ban-lines")
            ) . "\n";
        }
        $message .= str_replace("{mutes}", strval(count($casier->getMutes())), $config->get("casier-mute-title")) . "\n";
        foreach($casier->getMutes() as $mute){
            $message .= str_replace(
                    ["{date}", "{reason}", "{author}"],
                    [TimeHelper::timestampToDate($mute->getTime()), $mute->getReason(), $mute->getAuthorName()],
                    $config->get("casier-mute-lines")
                ) . "\n";
        }
        $sender->sendMessage($message);
    }
}
