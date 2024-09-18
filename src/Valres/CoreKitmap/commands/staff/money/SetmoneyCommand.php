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

namespace Valres\CoreKitmap\commands\staff\money;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;

class SetmoneyCommand extends Command
{
    public function __construct() {
        parent::__construct("set-money", "Défini de la money à un joueur", "usage : /set-money <player> <amount>");
        $this->setPermission("set-money.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $moneyManager = Core::getInstance()->moneyManager;
        $config = Core::getInstance()->getConfigFile(FilesManager::MONEY);

        if(count($args) < 2){
            $sender->sendMessage($this->getUsage());
            return;
        }

        $target = $args[0];
        $amount = floatval($args[1]);
        if($amount <= 0){
            $sender->sendMessage($config->get("not-valid-number-message"));
            return;
        }

        if(!$moneyManager->exist($target)){
            $sender->sendMessage($config->get("no-player-message"));
            return;
        }

        $moneyManager->setMoney($target, $amount);
        $sender->sendMessage(str_replace(
            ["{amount}", "{target}"],
            [$amount, $target],
            $config->get("player-set-money-message")
        ));

        $targetPlayer = Server::getInstance()->getPlayerExact($target);
        if($targetPlayer instanceof Player){
            $targetPlayer->sendMessage(str_replace(
                ["{amount}", "{player}"],
                [$amount, $target],
                $config->get("target-set-money-message")
            ));
        }
    }
}