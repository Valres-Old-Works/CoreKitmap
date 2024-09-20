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

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\box\Box;
use Valres\CoreKitmap\managers\files\FilesManager;

class BoxEntity extends Human
{
    private ?Box $box;

    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null) {
        parent::__construct($location, $skin, $nbt);

        $this->box = $nbt?->getTag("box") instanceof StringTag ? Core::getInstance()->boxManager->getBox($nbt->getString("box")) : null;
    }

    public function saveNBT(): CompoundTag {
        $nbt = parent::saveNBT();
        $nbt->setString("box", $this->box->getName());
        return $nbt;
    }

    public function setBox(Box $box): void {
        $this->box = $box;
    }

    public function attack(EntityDamageEvent $source): void {
        parent::attack($source);
        $source->cancel();

        $config = Core::getInstance()->getConfigFile(FilesManager::BOX);

        if(!$source instanceof EntityDamageByEntityEvent) return;
        $player = $source->getDamager();
        if(!$player instanceof Player) return;

        if($player->isCreative() and $player->isSneaking()){
            $this->flagForDespawn();
            return;
        }

        if(!$player->getInventory()->getItemInHand()->equals($this->box->getKey())){
            $player->sendMessage($config->get("no-key-message"));
            return;
        }

        $totalChance = 0;
        foreach($this->box->getBoxItems() as $boxItem){
            $totalChance += $boxItem->getChance();
        }

        $randomNumber = mt_rand(1, $totalChance);
        $currentChance = 0;
        $item = null;

        foreach($this->box->getBoxItems() as $boxItem){
            $currentChance += $boxItem->getChance();
            if($randomNumber <= $currentChance){
                $item = $boxItem;
                break;
            }
        }

        $player->getInventory()->addItem($item->getItem());
        $player->sendMessage(str_replace(
            ["{count}", "{item}", "{box}"],
            [$item->getItem()->getCount(), $item->getItem()->getName(), $this->box->getDisplayName()],
            $config->get("reward-message")
        ));
    }
}