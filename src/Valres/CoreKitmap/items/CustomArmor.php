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

namespace Valres\CoreKitmap\items;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use Valres\CoreKitmap\libs\customies\item\ItemComponents;
use Valres\CoreKitmap\libs\customies\item\component\DurabilityComponent;
use Valres\CoreKitmap\libs\customies\item\component\WearableComponent;
use Valres\CoreKitmap\libs\customies\item\CreativeInventoryInfo as CII;
use Valres\CoreKitmap\libs\customies\item\ItemComponentsTrait;

final class CustomArmor extends Armor implements ItemComponents
{
    use ItemComponentsTrait;

    /** @var EffectInstance[]|null */
    private ?array $effects;

    public function __construct(string $name, string $texture, ArmorTypeInfo $info, ?array $effects = null) {
        parent::__construct(
            new ItemIdentifier(ItemTypeIds::newId()),
            $name,
            $info
        );

        $this->effects = $effects;

        $this->initComponent($texture, new CII(CII::CATEGORY_EQUIPMENT, match($info->getArmorSlot()){
            0 => CII::GROUP_HELMET,
            1 => CII::GROUP_CHESTPLATE,
            2 => CII::GROUP_LEGGINGS,
            3 => CII::GROUP_BOOTS
        }));

        $this->addComponent(new DurabilityComponent($info->getMaxDurability()));
        $this->addComponent(new WearableComponent(match($info->getArmorSlot()){
            0 => WearableComponent::SLOT_ARMOR_HEAD,
            1 => WearableComponent::SLOT_ARMOR_CHEST,
            2 => WearableComponent::SLOT_ARMOR_LEGS,
            3 => WearableComponent::SLOT_ARMOR_FEET
        }, $info->getDefensePoints()));
    }
}