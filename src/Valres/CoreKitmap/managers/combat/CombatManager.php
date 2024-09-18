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

namespace Valres\CoreKitmap\managers\combat;

use pocketmine\utils\Config;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\BaseManager;
use Valres\CoreKitmap\managers\files\FilesManager;

class CombatManager extends BaseManager
{
    private float $knockback;
    private int   $attackCooldown;

    private int   $combatTime;

    private Config $config;


    public function getName(): string {
        return "Combat";
    }

    public function load(): void {
        $this->config = Core::getInstance()->getConfigFile(FilesManager::COMBAT);

        $this->knockback      = $this->config->get("knockback");
        $this->attackCooldown = $this->config->get("attack-cooldown");
        $this->combatTime     = $this->config->get("combat-time");
    }

    public function save(): void {
        $this->config->set("knockback", $this->knockback);
        $this->config->set("attack-cooldown", $this->attackCooldown);
    }

    public function getKnockback(): float {
        return $this->knockback;
    }

    public function setKnockback(float $knockback): void {
        $this->knockback = $knockback;
    }

    public function getAttackCooldown(): int {
        return $this->attackCooldown;
    }

    public function setAttackCooldown(int $attackCooldown): void {
        $this->attackCooldown = $attackCooldown;
    }

    public function getCombatTime(): int {
        return $this->combatTime;
    }
}