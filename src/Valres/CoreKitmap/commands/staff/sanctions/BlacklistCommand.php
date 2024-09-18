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

class BlacklistCommand extends Command
{
    public function __construct() {
        parent::__construct("blacklist", "Blacklist toutes les IPs et Uuids d'un joueur", "usage : /blacklist <player>");
        $this->setPermission("blacklist.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::SANCTIONS);
        if(count($args) < 1){
            $sender->sendMessage($this->getUsage());
            return;
        }

        $target = $args[0];
        $altManager = Core::getInstance()->accountManager;
        if(!$altManager->exist($target)){
            $sender->sendMessage($config->get("no-players"));
            return;
        }

        $blacklisteds = [];
        foreach($altManager->getIPs($target) as $ip){
            $blacklisteds[] = $ip;
            foreach($altManager->getPseudoByIP($ip) as $pseudo){
                $blacklisteds[] = $pseudo;
            }
        }
        foreach($altManager->getUUIDs($target) as $uuid){
            $blacklisteds[] = $uuid;
            foreach($altManager->getPseudoByUuid($uuid) as $pseudo){
                $blacklisteds[] = $pseudo;
            }
        }

        $sanctionsManager = Core::getInstance()->sanctionsManager;
        foreach($blacklisteds as $blacklisted){
            $sanctionsManager->addToBlacklist($blacklisted);
        }

        $sender->sendMessage(str_replace("{player}", $target, $config->get("blacklist")));
    }
}