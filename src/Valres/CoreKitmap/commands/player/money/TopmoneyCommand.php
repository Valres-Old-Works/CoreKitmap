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
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;

class TopmoneyCommand extends Command
{
    public function __construct() {
        parent::__construct("topmoney", "Voir le topmoney", "usage : /topmoney");
        $this->setPermission("topmoney.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $moneyManager = Core::getInstance()->moneyManager;
        $config = Core::getInstance()->getConfigFile(FilesManager::MONEY);

        $moneys = $moneyManager->getMoneys();
        arsort($moneys);

        $message = $config->get("topmoney-title") . "\n";

        $rank = 1;
        foreach(array_slice($moneys, 0, 10, true) as $playerName => $money){
            $message .= str_replace(
                ["{rank}", "{player}", "{money}"],
                [$rank++, $playerName, $money],
                $config->get("topmoney-lines")
            ) . "\n";
        }

        $sender->sendMessage($message);
    }

}