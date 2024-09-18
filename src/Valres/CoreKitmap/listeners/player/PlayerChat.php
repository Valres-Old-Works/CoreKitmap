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

namespace Valres\CoreKitmap\listeners\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent as Event;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\utils\TimeHelper;

class PlayerChat implements Listener
{
    public function onEvent(Event $event): void {
        $player = $event->getPlayer();

        $sanctionManager = Core::getInstance()->sanctionsManager;
        if($sanctionManager->isMuted($player->getName())){
            $mute = $sanctionManager->getMute($player->getName());
            if($mute->getTime() < time()){
                $sanctionManager->removeMute($player->getName());
                return;
            }

            $event->cancel();
            $player->sendMessage(str_replace(
                ["{time}", "{reason}"],
                [TimeHelper::timeToString($mute->getTime()), $mute->getReason()],
                Core::getInstance()->getConfigFile(FilesManager::SANCTIONS)->get("mute-player-message")
            ));
        }

    }
}
