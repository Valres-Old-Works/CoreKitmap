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

namespace Valres\CoreKitmap\player;

class Settings
{
    public function __construct(
        protected bool $scoreboard = false,
        protected bool $cps        = false,
        protected bool $combo      = false,
        protected bool $reach      = false,
        protected bool $sprint     = false,
        protected bool $mp         = false
    ) {}

    public function isScoreboard(): bool {
        return $this->scoreboard;
    }

    public function isCps(): bool {
        return $this->cps;
    }

    public function isCombo(): bool {
        return $this->combo;
    }

    public function isReach(): bool {
        return $this->reach;
    }

    public function isSprint(): bool {
        return $this->sprint;
    }

    public function isMp(): bool {
        return $this->mp;
    }

    public function setScoreboard(bool $scoreboard): void {
        $this->scoreboard = $scoreboard;
    }

    public function setCps(bool $cps): void {
        $this->cps = $cps;
    }

    public function setCombo(bool $combo): void {
        $this->combo = $combo;
    }

    public function setReach(bool $reach): void {
        $this->reach = $reach;
    }

    public function setSprint(bool $sprint): void {
        $this->sprint = $sprint;
    }

    public function setMp(bool $mp): void {
        $this->mp = $mp;
    }
}