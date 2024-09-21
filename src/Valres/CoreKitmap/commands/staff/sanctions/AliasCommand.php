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

class AliasCommand extends Command
{
    public function __construct() {
        parent::__construct("alias", "Voir les DCs d'un joueur", "usage : /alias <player>");
        $this->setPermission("alias.command");
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

        $pseudos = [];
        $ips = $altManager->getIPs($target);
        $uuids = $altManager->getUUIDs($target);

        foreach($ips as $ip){
            $pseudos = array_merge($pseudos, $altManager->getPseudoByIP($ip));
        }

        foreach($uuids as $uuid){
            $pseudos = array_merge($pseudos, $altManager->getPseudoByUuid($uuid));
        }

        $pseudos = array_unique($pseudos);

        $message  = str_replace("{player}", $target, $config->get("alias-title")) . "\n";
        $message .= $config->get("alias-account-title") . "\n";
        foreach($pseudos as $pseudo){
            $message .= str_replace("{pseudo}", $pseudo, $config->get("alias-account-lines")) . "\n";
        }
        $message .= $config->get("alias-ip-title") . "\n";
        foreach($ips as $ip){
            $message .= str_replace("{ip}", $ip, $config->get("alias-ip-lines")) . "\n";
        }
        $message .= $config->get("alias-uuid-title") . "\n";
        foreach($uuids as $uuid){
            $message .= str_replace("{uuid}", $uuid, $config->get("alias-uuid-lines")) . "\n";
        }

        $sender->sendMessage($message);
    }

}