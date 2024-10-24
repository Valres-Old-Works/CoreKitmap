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

namespace Valres\CoreKitmap\utils;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\box\BoxItem;

class Utils
{
    public static function callDirectory(string $directory, callable $callable): void {
        $main = explode("\\", Core::getInstance()->getDescription()->getMain());
        unset($main[array_key_last($main)]);
        $main = implode("/", $main);
        $directory = rtrim(str_replace(DIRECTORY_SEPARATOR, "/", $directory), "/");
        $dir = Core::getInstance()->file . "src/$main/" . $directory;


        foreach(array_diff(scandir($dir), [".", ".."]) as $file){
            $path = $dir . "/$file";
            $extension = pathinfo($path)["extension"] ?? null;

            if($extension === null){
                self::callDirectory($directory . "/" . $file, $callable);
            } elseif($extension === "php"){
                $namespaceDirectory = str_replace("/", "\\", $directory);
                $namespaceMain = str_replace("/", "\\", $main);
                $namespace = $namespaceMain . "\\$namespaceDirectory\\" . basename($file, ".php");
                $callable($namespace);
            }
        }
    }

    public static function makeItem(Item $item, array $data): Item {
        if(isset($data["customName"])){
            $item->setCustomName($data["customName"]);
        }

        if(isset($data["enchants"])){
            foreach($data["enchants"] as $enchant){
                $enchant = explode(":", $enchant);
                $item->addEnchantment(new EnchantmentInstance(StringToEnchantmentParser::getInstance()->parse($enchant[0]), intval($enchant[1])));
            }
        }

        return $item;
    }

    public static function getEnchantName(Enchantment $enchantment): string {
        $enchants = [
            VanillaEnchantments::PROTECTION()->getName()->getText() => "protection",
            VanillaEnchantments::FIRE_PROTECTION()->getName()->getText() => "fire_protection",
            VanillaEnchantments::FEATHER_FALLING()->getName()->getText() => "feather_falling",
            VanillaEnchantments::BLAST_PROTECTION()->getName()->getText() => "blast_protection",
            VanillaEnchantments::PROJECTILE_PROTECTION()->getName()->getText() => "projectile_protection",
            VanillaEnchantments::RESPIRATION()->getName()->getText() => "respiration",
            VanillaEnchantments::THORNS()->getName()->getText() => "thorns",
            VanillaEnchantments::SHARPNESS()->getName()->getText() => "sharpness",
            VanillaEnchantments::KNOCKBACK()->getName()->getText() => "knockback",
            VanillaEnchantments::FIRE_ASPECT()->getName()->getText() => "fire_aspect",
            VanillaEnchantments::EFFICIENCY()->getName()->getText() => "efficiency",
            VanillaEnchantments::SILK_TOUCH()->getName()->getText() => "silk_touch",
            VanillaEnchantments::UNBREAKING()->getName()->getText() => "unbreaking",
            VanillaEnchantments::FORTUNE()->getName()->getText() => "fortune",
            VanillaEnchantments::POWER()->getName()->getText() => "power",
            VanillaEnchantments::PUNCH()->getName()->getText() => "punch",
            VanillaEnchantments::FLAME()->getName()->getText() => "flame",
            VanillaEnchantments::INFINITY()->getName()->getText() => "infinity",
            VanillaEnchantments::MENDING()->getName()->getText() => "mending"
        ];

        return $enchants[$enchantment->getName()->getText()] ?? "unknown_enchantment";
    }


    public static function unmakeItem(BoxItem $boxItem): array {
        $item = $boxItem->getItem();
        $array = [];
        $array["count"] = $item->getCount();
        $array["customName"] = $item->getName();
        if(count($item->getEnchantments()) > 0){
            foreach($item->getEnchantments() as $enchantment){
                $array["enchants"][] = self::getEnchantName($enchantment->getType()) . ":" . $enchantment->getLevel();
            }
        }
        $array["chance"] = $boxItem->getChance();

        return $array;
    }

    public static function PNGtoBYTES($path) : string {
        $img = @imagecreatefrompng($path);
        $bytes = "";
        $L = (int)@getimagesize($path)[0];
        $l = (int)@getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < $L; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~($rgba >> 24)) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    public static function getHealBar(float $number, float $max, string $color): string {
        $greenBars = $color . str_repeat("|", intval(($number / $max) * 20));
        $grayBars = (strlen($greenBars) < 20 ? "§8" . str_repeat("|", 20 - strlen($greenBars)) : "");
        return $greenBars . $grayBars . "§r";
    }
}
