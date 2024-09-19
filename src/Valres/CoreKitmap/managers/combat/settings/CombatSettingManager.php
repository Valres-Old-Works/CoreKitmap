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

namespace Valres\CoreKitmap\managers\combat\settings;

use Ahc\Json\Comment;
use pocketmine\player\Player;
use Valres\CoreKitmap\managers\BaseManager;

class CombatSettingManager extends BaseManager
{
    /** @var CombatInterface[] */
    private array $interfaces = [];

    public function getName(): string {
        return "Combat Settings";
    }

    public function load(): void {}

    public function save(): void {}

    public function getInterface(string $playerName): ?CombatInterface {
        return $this->interfaces[$playerName] ?? null;
    }

    public function exist(string $playerName): bool {
        return array_key_exists($playerName, $this->interfaces);
    }

    public function register(Player $player): void {
        $this->interfaces[$player->getName()] = new CombatInterface($player);
    }
}