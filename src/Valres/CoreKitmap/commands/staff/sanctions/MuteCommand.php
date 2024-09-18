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
use Valres\CoreKitmap\managers\sanctions\types\Mute;
use Valres\CoreKitmap\utils\TimeHelper;

class MuteCommand extends Command
{
    public function __construct() {
        parent::__construct("mute", "Mute un joueur", "usage : /mute un joueur");
        $this->setPermission("mute.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile("sanctions-config");
        if(count($args) < 3){
            $sender->sendMessage($this->getUsage());
            return;
        }

        $playerName = $args[0];
        $time = TimeHelper::stringToTime($args[1]);
        $reason = implode(" ", array_slice($args, 2));

        $sanctionsManager = Core::getInstance()->sanctionsManager;
        if($sanctionsManager->isMuted($playerName)){
            $sender->sendMessage($config->get("already-mute"));
            return;
        }

        $sanctionsManager->addMute(new Mute($playerName, $reason, $time, $sender->getName()), true);
    }
}