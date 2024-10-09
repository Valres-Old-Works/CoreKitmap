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

namespace Valres\CoreKitmap\entity\events;

use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use Valres\CoreKitmap\entity\FloatingText;
use Valres\CoreKitmap\managers\events\types\Outpost;

class OutpostFloatingText extends FloatingText
{
    public ?Outpost $outpost = null;

    public function __construct(Location $location, ?CompoundTag $nbt = null) {
        parent::__construct($location, $nbt);
    }

    public function setOutpost(Outpost $outpost): self {
        $this->outpost = $outpost;
        return $this;
    }

    public function onUpdate(int $currentTick): bool {
        if(is_null($this->outpost)) return false;
        $faction = $this->outpost->getFaction();
        $conquered = !is_null($faction);
        $captureTime = $this->outpost->getCapturedTime();

        $factionName = $faction ? $faction->getName() : "Aucune";
        $text = "Outpost\n§tFaction:§r " . $factionName . "\n";

        if($conquered){
            $timeInfo = $captureTime > 0
                ? "§tTemps de capture:§r " . $this->durationToShortString(60 - $captureTime)
                : "§tRécompenses:§r " . $this->durationToShortString($this->outpost->getRewardTimeRemaining());
        } else $timeInfo = "§tTemps de capture:§r " . $this->durationToShortString(180 - $captureTime);

        $text .= $timeInfo;
        $this->setText($text);

        return parent::onUpdate($currentTick);
    }

    public function durationToShortString(int $t): string {
        $s = $t % 60;
        $t = ($t - $s) / 60;
        $m = $t % 60;
        $h = ($t - $m) / 60;

        $string = "";

        if($h > 0) $string .= trim($h . "h") . " ";
        if($m > 0) $string .= trim($m . "m") . " ";
        if($s > 0) $string .= trim($s . "s") . " ";


        return trim($string);
    }
}