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

namespace Valres\CoreKitmap\commands\player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;

class StatsCommand extends Command
{
    public function __construct() {
        parent::__construct("stats", "Voir les stats d'un joueur", "usage : /stats <player>");
        $this->setPermission("stats.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::STATS);

        $target = $args[0] ?? $sender->getName();
        $statsManager = Core::getInstance()->statisticsManager;

        if(!$statsManager->exist($target)){
            $sender->sendMessage($config->get("no-player"));
            return;
        }
        $stats = $statsManager->getStats($target);

        $message  = str_replace("{player}", $target, $config->get("stats-title")) . "\n";
        $message .= str_replace("{kills}", strval($stats->getKills()), $config->get("stats-kill-line")) . "\n";
        $message .= str_replace("{death}", strval($stats->getDeath()), $config->get("stats-death-line")) . "\n";
        $message .= str_replace("{kdr}", strval($stats->getKDR()), $config->get("stats-kdr-line")) . "\n";

        $sender->sendMessage($message);
    }
}