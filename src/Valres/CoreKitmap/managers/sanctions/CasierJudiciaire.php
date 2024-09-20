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

namespace Valres\CoreKitmap\managers\sanctions;

use Valres\CoreKitmap\managers\sanctions\types\Ban;
use Valres\CoreKitmap\managers\sanctions\types\IPBan;
use Valres\CoreKitmap\managers\sanctions\types\Mute;
use Valres\CoreKitmap\managers\sanctions\types\UuidBan;

class CasierJudiciaire
{
    public function __construct(
        protected array $bans  = [],
        protected array $mutes = []
    ) {}

    public function getBans(): array {
        return $this->bans;
    }

    public function addBan(UuidBan|Ban|IPBan $ban): void {
        $this->bans[] = $ban;
    }

    public function getMutes(): array {
        return $this->mutes;
    }

    public function addMute(Mute $mute): void {
        $this->mutes[] = $mute;
    }
}
