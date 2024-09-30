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

namespace Valres\CoreKitmap\managers\shop;

use JsonException;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Valres\CoreKitmap\libs\invmenu\InvMenu;
use Valres\CoreKitmap\libs\invmenu\transaction\DeterministicInvMenuTransaction;
use Valres\CoreKitmap\libs\invmenu\type\InvMenuTypeIds;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;
use Valres\CoreKitmap\managers\BaseManager;

class ShopManager extends BaseManager
{
    private array $shop = [];

    private Config $datas;

    public function getName(): string {
        return "Shop";
    }

    public function load(): void {
        $this->datas = new Config($this->getPlugin()->getDataFolder() . "shop/shop.yml");

        foreach($this->datas->getAll() as $catName => ["itemDisplay" => $itemDisplay, "items" => $items]){
            $itemList = [];
            foreach($items as $item){
                $itemList[] = new ShopItem(
                    $item["item"],
                    $item["buyPrice"],
                    $item["sellPrice"]
                );
            }
            $this->shop[$catName] = [
                "itemDisplay" => $itemDisplay,
                "items" => $itemList
            ];
        }
    }

    /** @throws JsonException */
    public function save(): void {
        foreach($this->shop as $catName => ["itemDisplay" => $itemDisplay, "items" => $items]){
            $itemList = [];
            foreach($items as $item){
                $itemList[] = [
                    "item" => $item->getItemParse(),
                    "buyPrice" => $item->getBuyPrice(),
                    "sellPrice" => $item->getSellPrice()
                ];
            }
            $this->datas->set($catName, [
                "itemDisplay" => $itemDisplay,
                "items" => $itemList
            ]);
        }
        $this->datas->save();
    }

    public function getCategories(): array {
        return array_keys($this->shop);
    }

    public function getCategory(string $name): ?array {
        return $this->shop[$name] ?? null;
    }

    public function getNameCategory(int|string $index): ?string {
        $categories = array_keys($this->shop);

        if(isset($categories[$index])) {
            return $categories[$index];
        }

        return null;
    }

    public function addCategory(string $catName, string $itemDisplay): void {
        $this->shop[$catName] = [
            "itemDisplay" => $itemDisplay,
            "items" => []
        ];
    }

    public function removeCategory(string $catName): void {
        unset($this->shop[$catName]);
    }

    public function getShopItem(string $catName, int $index): ?ShopItem {
        var_dump($this->shop);
        if(!isset($this->shop[$catName])){
            return null;
        }
        return $this->shop[$catName]["items"][$index] ?? null;
    }

    public function addShopItem(string $catName, ShopItem $shopItem): void {
        $this->shop[$catName]["items"][] = $shopItem;
    }

    public function removeShopItem(string $catName, int $data): void {
        unset($this->shop[$catName]["items"][$data]);
    }

    public function sendMainMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $panes = [0, 1, 7, 8, 9, 17, 36, 44, 45, 46, 52, 53];
        foreach($panes as $pane){
            $menu->getInventory()->setItem($pane, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK)->asItem());
        }
        $start = 11;
        foreach($this->shop as $catName => ["itemDisplay" => $itemDisplay, "items" => $items]){
            $menu->getInventory()->setItem($start, StringToItemParser::getInstance()->parse($itemDisplay)->setCustomName("§r" . $catName));
            $start++;
            if(in_array($start, [16, 25, 34, 43])){
                $start += 4;
            }
        }
        $menu->setListener(InvMenu::readonly(function(DeterministicInvMenuTransaction $transaction) use ($menu): void {
            $slot = $transaction->getAction()->getSlot();
            $catName = str_replace("§r", "", $transaction->getAction()->getSourceItem()->getName());
            if(!in_array($slot, [11, 12, 13, 14, 15] + [20, 21, 22, 23, 24] + [29, 30, 31, 32, 33] + [38, 39, 40, 41, 42])) return;
            $category = $this->getCategory($catName);
            if(is_null($category)) return;

            $menu->getInventory()->clearAll();
            /** @var ShopItem $shopItem */
            foreach($category["items"] as $shopItem){
                $menu->getInventory()->addItem($shopItem->getItem()->setLore([
                    "Prix d'achat :$" . (is_null($shopItem->getBuyPrice()) ? "Non-achetable" : $shopItem->getBuyPrice()),
                    "Prix de vente :$" . (is_null($shopItem->getSellPrice()) ? "Non-vendable" : $shopItem->getSellPrice())
                ]));
                $menu->setListener(InvMenu::readonly(function(DeterministicInvMenuTransaction $transaction) use ($catName): void {
                    $transaction->getTransaction()->getSource()->removeCurrentWindow();
                    $slot = $transaction->getAction()->getSlot();
                    $this->sendForm($transaction->getTransaction()->getSource(), $this->getShopItem($catName, $slot));
                }));
            }
        }));

        $menu->send($player, "Shop");
    }

    public function sendForm(Player $player, ShopItem $item): void {
        $form = new CustomForm(function(Player $player, array $data = null): void {
            if(is_null($data)) return;
        });
        $form->setTitle($item->getItem()->getName());
        $form->addLabel(join("\n", [
            "Prix d'achat: $" . (is_null($item->getBuyPrice()) ? "Non-achetable" : $item->getBuyPrice()),
            "Prix de vente :$" . (is_null($item->getSellPrice()) ? "Non-vendable" : $item->getSellPrice())
        ]));
        $form->addInput("Nombre :");
        $form->addToggle("Achat/vente");
        $player->sendForm($form);
    }
}