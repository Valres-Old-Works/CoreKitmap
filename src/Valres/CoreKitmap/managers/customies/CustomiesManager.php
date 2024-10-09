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

namespace Valres\CoreKitmap\managers\customies;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\item\ArmorTypeInfo;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\items\CustomArmor;
use Valres\CoreKitmap\items\tools\CustomAxe;
use Valres\CoreKitmap\items\tools\CustomHoe;
use Valres\CoreKitmap\items\tools\CustomPickaxe;
use Valres\CoreKitmap\items\tools\CustomShovel;
use Valres\CoreKitmap\items\tools\CustomSword;
use Valres\CoreKitmap\libs\customies\item\CustomiesItemFactory;
use Valres\CoreKitmap\managers\BaseManager;

class CustomiesManager extends BaseManager
{
    const AXE = "axe";
    const HOE = "hoe";
    const PICKAXE = "pickaxe";
    const SHOVEL = "shovel";
    const SWORD = "sword";

    public function getName(): string {
        return "Customies";
    }

    public function load(): void {
        $factory = CustomiesItemFactory::getInstance();
        $armors  = Core::getInstance()->getConfigFile("items/armors.yml");
        $tools   = Core::getInstance()->getConfigFile("items/tools.yml");

        foreach($armors->getAll() as $identifier => $armor){
            $effects = [];
            if(isset($armor["effects"])){
                foreach($armors["effects"] as $effect){
                    $effect = explode(":", $effect);
                    $effects[] = new EffectInstance(StringToEffectParser::getInstance()->parse($effect[0]), 2147483647, intval($effect[1]) - 1, false);
                }
            }
            $factory->registerItem(new CustomArmor(
                $armor["name"],
                $armor["texture"],
                new ArmorTypeInfo($armor["protection"], $armor["durability"], $armor["slot"]),
                (count($effects) >= 1 ? $effects : null)
            ), "minecraft:" . $identifier);
        }

        foreach($tools->getAll() as $identifier => $tool){
            switch($tool["type"]){
                case self::PICKAXE:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomPickaxe(
                        $tool["name"],
                        $tool["texture"],
                        $tool["efficiency"],
                        $tool["damage"],
                        $tool["durability"]
                    ), "minecraft:" . $identifier);
                    break;
                case self::AXE:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomAxe(
                        $tool["name"],
                        $tool["texture"],
                        $tool["efficiency"],
                        $tool["damage"],
                        $tool["durability"]
                    ), "minecraft:" . $identifier);
                    break;
                case self::HOE:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomHoe(
                        $tool["name"],
                        $tool["texture"],
                        $tool["efficiency"],
                        $tool["damage"],
                        $tool["durability"]
                    ), "minecraft:" . $identifier);
                    break;
                case self::SHOVEL:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomShovel(
                        $tool["name"],
                        $tool["texture"],
                        $tool["efficiency"],
                        $tool["damage"],
                        $tool["durability"]
                    ), "minecraft:" . $identifier);
                    break;
                case self::SWORD:
                    CustomiesItemFactory::getInstance()->registerItem(new CustomSword(
                        $tool["name"],
                        $tool["texture"],
                        $tool["efficiency"],
                        $tool["damage"],
                        $tool["durability"]
                    ), "minecraft:" . $identifier);
                    break;
            }
        }
    }

    public function save(): void {}
}