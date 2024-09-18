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

namespace Valres\CoreKitmap\commands\player\money;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;

class PayCommand extends Command
{
    public function __construct() {
        parent::__construct("pay", "Payer un joueur", "usage : /pay <player> <amount>");
        $this->setPermission("pay.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $moneyManager = Core::getInstance()->moneyManager;
        $config = Core::getInstance()->getConfigFile(FilesManager::MONEY);

        if(!$sender instanceof Player) return;
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

        if($moneyManager->getMoney($sender->getName()) < $amount){
            $sender->sendMessage($config->get("no-money-message"));
            return;
        }

        $moneyManager->reduceMoney($sender->getName(), $amount);
        $sender->sendMessage(str_replace(
            ["{amount}", "{target}"],
            [$amount, $target],
            $config->get("player-pay-message")
        ));

        $moneyManager->addMoney($target, $amount);
        $targetPlayer = Server::getInstance()->getPlayerExact($target);
        if($targetPlayer instanceof Player){
            $targetPlayer->sendMessage(str_replace(
                ["{amount}", "{player}"],
                [$amount, $sender->getName()],
                $config->get("target-pay-message")
            ));
        }
    }
}