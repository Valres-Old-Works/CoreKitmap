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

class CombatInterface
{
    public function __construct(
        protected array $cps    = [],
        protected int   $combos = 0,
        protected float $reach  = 0.0
    ) {}

    public function getCps(): int {
        return count(array_filter($this->cps, function($timestamp): bool {
            return (microtime(true) - $timestamp) <= 1;
        }));
    }

    public function addCps(): void {
        array_unshift($this->cps, microtime(true));
        if(count($this->cps) > 100){
            array_pop($this->cps);
        }
    }

    public function getCombos(): int {
        return $this->combos;
    }

    public function addCombo(): void {
        $this->combos ++;
    }

    public function resetCombo(): void {
        $this->combos = 0;
    }

    public function getReach(): float {
        return round($this->reach, 2);
    }

    public function setReach(float $reach): void {
        $this->reach = $reach;
    }
}