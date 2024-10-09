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

namespace Valres\CoreKitmap\entity;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use Valres\CoreKitmap\player\CustomPlayer;

class FloatingText extends Living
{
    protected string $text = "";

    protected bool $gravityEnabled = false;
    protected float $gravity = 0.0;

    public function __construct(Location $location, ?CompoundTag $nbt = null) {
        parent::__construct($location, $nbt);
        $this->setNameTagVisible(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(0.001);
        $this->setGravity(0);

        $this->boundingBox = new AxisAlignedBB(0, 0, 0, 0, 0, 0);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(0.0, 0.0);
    }

    public function attack(EntityDamageEvent $source): void {
        if(!$source instanceof EntityDamageByEntityEvent) return;

        /** @var CustomPlayer $damager */
        $damager = $source->getDamager();
        if(!$damager instanceof Player) return;
        if(!$source->getEntity() instanceof $this) return;

        if($damager->isOp()){
            if($damager->isSneaking() && $damager->getGamemode() === GameMode::CREATIVE()){
                $source->getEntity()->flagForDespawn();
            }
        }
    }

    public function setText(string $text): void {
        $this->text = $text;
        $this->setNameTag($this->text);
    }

    public function tryChangeMovement(): void {}

    protected function getInitialDragMultiplier(): float {
        return 0.0;
    }

    protected function getInitialGravity(): float {
        return 0.0;
    }

    public static function getNetworkTypeId(): string {
        return EntityIds::CHICKEN;
    }

    public function getName(): string {
        return "Floating text";
    }
}