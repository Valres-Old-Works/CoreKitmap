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
use pocketmine\event\player\PlayerPreLoginEvent as Event;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\utils\TimeHelper;

class PlayerPreLogin implements Listener
{
    public function onEvent(Event $event): void {
        $name   = $event->getPlayerInfo()->getUsername();
        $uuid   = $event->getPlayerInfo()->getUuid()->toString();
        $ip     = $event->getIp();
        $config = Core::getInstance()->getConfigFile(FilesManager::SANCTIONS);

        $sanctionsManager = Core::getInstance()->sanctionsManager;

        if($sanctionsManager->isBlacklist($name) or $sanctionsManager->isBlacklist($ip) or $sanctionsManager->isBlacklist($uuid)){
            $event->setKickFlag(Event::KICK_FLAG_BANNED, $config->get("blacklist-login-message"));
            return;
        }

        $ban = null;
        if($sanctionsManager->isBanned($name)){
            $ban = $sanctionsManager->getBan($name);
        }
        if($sanctionsManager->isIPBanned($ip)){
            $ban = $sanctionsManager->getIPBan($ip);
        }
        if($sanctionsManager->isUuidBanned($uuid)){
            $ban = $sanctionsManager->getUuidBan($uuid);
        }

        if(!is_null($ban)){
            if($ban->getTime() < time()){
                $sanctionsManager->removeBan($name);
                return;
            }

            $event->setKickFlag(Event::KICK_FLAG_BANNED, str_replace(
                ["{reason}", "{time}", "{author}"],
                [$ban->getReason(), TimeHelper::timeToString($ban->getTime()), $ban->getAuthorName()],
                $config->get("ban-login-message")
            ));
        }

        $altAccountsManager = Core::getInstance()->accountManager;
        if(!$altAccountsManager->exist($name)){
            $altAccountsManager->register($name, $ip, $uuid);
        }

        $moneyManager = Core::getInstance()->moneyManager;
        if(!$moneyManager->exist($name)){
            $moneyManager->register($name);
        }
    }
}
