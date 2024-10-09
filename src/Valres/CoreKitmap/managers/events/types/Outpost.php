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

namespace Valres\CoreKitmap\managers\events\types;

use pocketmine\Server;
use pocketmine\world\World;

class Outpost
{
    private string $name = "";
    private ?string $faction = null;
    private ?int $rewardTime = null;
    private int $captureTime = 0;
    private ?string $captureFaction = null;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getFaction(): ?Faction {
        //TODO : Faction
    }

    public function setFaction(?string $faction): void {
        $this->faction = $faction;
    }

    public function getWorld(): ?World {
        return Server::getInstance()->getWorldManager()->getDefaultWorld();
    }

    public function getRewardTimeRemaining(): ?int {
        return 300 - (time() - $this->rewardTime);
    }

    public function setRewardTime(?int $rewardTime): void {
        $this->rewardTime = $rewardTime;
    }

    public function giveReward(): void {
        // TODO: reward
    }

    public function setCapturedTime(int $time): void {
        $this->captureTime = $time;
    }

    public function getCapturedTime(): int {
        return $this->captureTime;
    }

    public function setCaptureFaction(?string $faction): void {
        $this->captureFaction = $faction;
    }

    public function getCaptureFaction(): ?string {
        return $this->captureFaction;
    }
}