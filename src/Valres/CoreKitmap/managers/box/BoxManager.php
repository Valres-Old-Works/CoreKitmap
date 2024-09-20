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

namespace Valres\CoreKitmap\managers\box;

use JsonException;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\Config;
use Valres\CoreKitmap\managers\BaseManager;
use Valres\CoreKitmap\utils\Utils;

class BoxManager extends BaseManager
{
    /** @var Box[] */
    private array $box = [];

    private Config $datas;

    public function getName(): string {
        return "Box";
    }

    public function load(): void {
        $this->datas = new Config($this->getPlugin()->getDataFolder() . "box/box.yml", Config::YAML);

        foreach($this->datas->getAll() as $name => $data){
            $boxItem = [];
            foreach($data["items"] as $itemName => $_data){
                $item = StringToItemParser::getInstance()->parse($itemName)->setCount($_data["count"]);
                $boxItem[] = new BoxItem(Utils::makeItem($item, $_data), $_data["chance"]);
            }

            $this->box[$name] = new Box(
                $name,
                $data["displayName"],
                $boxItem,
                $data["texturePath"],
                $data["geometryPath"],
                $data["geometryName"],
                StringToItemParser::getInstance()->parse($data["key"])
            );
        }
    }

    public function exist(string $name): bool {
        return array_key_exists($name, $this->box);
    }

    public function getBox(string $name): ?Box {
        return $this->box[$name] ?? null;
    }

    /** @throws JsonException */
    public function save(): void {
        $this->datas->setAll([]);
        foreach($this->box as $name => $box){
            $itemsArray = [];
            foreach($box->getBoxItems() as $boxItem){
                $itemsArray[StringToItemParser::getInstance()->lookupAliases($boxItem->getItem())[0]] = Utils::unmakeItem($boxItem);
            }
            $this->datas->set($name, [
                "displayName" => $box->getDisplayName(),
                "items" => $itemsArray,
                "texturePath" => $box->getTextureName(),
                "geometryPath" => $box->getGeometryPath(),
                "geometryName" => $box->getGeometryName(),
                "key" => StringToItemParser::getInstance()->lookupAliases($box->getKey())[0],
            ]);
        }
        $this->datas->save();
    }
}