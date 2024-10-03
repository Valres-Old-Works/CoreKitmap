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

use InvalidArgumentException;
use JsonException;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\libs\invmenu\InvMenu;
use Valres\CoreKitmap\libs\invmenu\transaction\DeterministicInvMenuTransaction;
use Valres\CoreKitmap\libs\invmenu\type\InvMenuTypeIds;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;
use Valres\CoreKitmap\managers\BaseManager;
use Valres\CoreKitmap\managers\files\FilesManager;

class ShopManager extends BaseManager
{
    private array $catSlot = [
        11, 12, 13, 14, 15,
        20, 21, 22, 23, 24,
        29, 30, 31, 32, 33,
        38, 39, 40, 41, 42
    ];
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
        $this->datas->setAll([]);
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

    public function changeCategoryName(string $oldName, string $newName): void {
        $orderedShop = [];

        foreach($this->shop as $name => $category){
            if($name === $oldName){
                $orderedShop[$newName] = $category;
            } else $orderedShop[$name] = $category;
        }

        $this->shop = $orderedShop;
    }

    public function modifyShopItem(string $catName, int $index, ShopItem $newShopItem): void {
        $this->shop[$catName]["items"][$index] = $newShopItem;
    }

    public function changeCategoryItemDisplay(string $catName, string $newItemDisplay): void {
        $this->shop[$catName]["itemDisplay"] = $newItemDisplay;
    }

    /** @return ShopItem[] */
    public function getShopItems(string $catName): array {
        return $this->shop[$catName]["items"];
    }

    public function getShopItem(string $catName, int $slot): ?ShopItem {
        if(!isset($this->shop[$catName])){
            return null;
        }
        return $this->shop[$catName]["items"][$slot] ?? null;
    }

    public function addShopItem(string $catName, ShopItem $shopItem): void {
        $this->shop[$catName]["items"][] = $shopItem;
    }

    public function removeShopItem(string $catName, int $index): void {
        unset($this->shop[$catName]["items"][$index]);
        $this->shop[$catName]["items"] = array_values($this->shop[$catName]["items"]);
    }

    public function sendMainMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $this->makeMainMenu($menu);
        $menu->send($player, "Shop");
    }

    public function makeMainMenu(InvMenu $menu): void {
        $panes = [0, 1, 7, 8, 9, 17, 36, 44, 45, 46, 52, 53];
        foreach($panes as $pane){
            $menu->getInventory()->setItem($pane, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK)->asItem());
        }
        $slot = 0;
        foreach($this->shop as $catName => $category) {
            $itemDisplay = $category['itemDisplay'];
            $menu->getInventory()->setItem(
                $this->catSlot[$slot],
                StringToItemParser::getInstance()->parse($itemDisplay)->setCustomName("§r" . $catName)
            );
            $slot++;
        }
        $menu->setListener(InvMenu::readonly(function(DeterministicInvMenuTransaction $transaction) use ($menu): void {
            $slot = $transaction->getAction()->getSlot();
            $catName = str_replace("§r", "", $transaction->getAction()->getSourceItem()->getName());
            if(!in_array($slot, $this->catSlot)) return;
            $category = $this->getCategory($catName);
            if(is_null($category)) return;

            $menu->getInventory()->clearAll();
            $this->makeCatMenu($menu, $catName);
        }));
    }

    public function makeCatMenu(InvMenu $menu, string $catName, int $page = 1): void {
        $category = $this->getCategory($catName);
        $panes = [0, 1, 7, 8, 9, 17, 36, 44, 45, 46, 52, 53];
        foreach ($panes as $pane) {
            $menu->getInventory()->setItem($pane, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK)->asItem());
        }
        $menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cRetour"));

        $itemsPerPage = count($this->catSlot);
        $totalItems = count($category["items"]);
        $totalPages = (int)ceil($totalItems / $itemsPerPage);

        if($page > 1){
            $menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§ePage précédente"));
        }
        if($page < $totalPages){
            $menu->getInventory()->setItem(50, VanillaItems::ARROW()->setCustomName("§r§ePage suivante"));
        }

        $startIndex = ($page - 1) * $itemsPerPage;
        $endIndex = min($startIndex + $itemsPerPage, $totalItems);
        $slot = 0;

        for($i = $startIndex; $i < $endIndex; $i++){
            $shopItem = $category["items"][$i];
            $lore = [];
            foreach (Core::getInstance()->getConfigFile(FilesManager::SHOP)->get("in-shop-lore") as $line) {
                $lore[] = str_replace(
                    ["{buy-price}", "{sell-price}"],
                    [(is_null($shopItem->getBuyPrice()) ? "§cNon-achetable" : $shopItem->getBuyPrice()), (is_null($shopItem->getSellPrice()) ? "§cNon-vendable" : $shopItem->getSellPrice())],
                    $line
                );
            }
            $menu->getInventory()->setItem(
                $this->catSlot[$slot],
                $shopItem->getItem()->setLore($lore)
            );
            $slot++;
        }

        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($catName, $menu, $page, $totalPages): void {
            $slot = $transaction->getAction()->getSlot();
            if($slot === 49){
                $menu->getInventory()->clearAll();
                $this->makeMainMenu($menu);
                return;
            }

            if($slot === 48 && $page > 1){
                $menu->getInventory()->clearAll();
                $this->makeCatMenu($menu, $catName, $page - 1);
                return;
            }
            if($slot === 50 && $page < $totalPages){
                $menu->getInventory()->clearAll();
                $this->makeCatMenu($menu, $catName, $page + 1);
                return;
            }

            $itemIndex = ($page - 1) * count($this->catSlot) + array_search($slot, $this->catSlot);
            $shopItem = $this->getShopItem($catName, $itemIndex);
            if($shopItem !== null){
                $transaction->getTransaction()->getSource()->removeCurrentWindow();
                $this->sendForm($transaction->getTransaction()->getSource(), $shopItem);
            }
        }));
    }

    public function sendForm(Player $player, ShopItem $item): void {
        $form = new CustomForm(function(Player $player, array $data = null) use ($item): void {
            if(is_null($data)) return;
            $config = Core::getInstance()->getConfigFile(FilesManager::SHOP);

            $amount = intval($data[1]);
            $sell   = $data[2];
            $moneyManager = Core::getInstance()->moneyManager;

            if($amount <= 0){
                $player->sendMessage($config->get("positif-amount"));
                return;
            }

            if(!$sell){
                if(is_null($item->getBuyPrice())){
                    $player->sendMessage($config->get("not-buyable"));
                    return;
                }

                $total = $amount * $item->getBuyPrice();

                if($moneyManager->getMoney($player->getName()) < $total){
                    $player->sendMessage($config->get("no-money"));
                    return;
                }

                $moneyManager->reduceMoney($player->getName(), $total);
                $items = $item->getItem()->setCount($amount);
                if($player->getInventory()->canAddItem($items)){
                    $player->getInventory()->addItem($items);
                } else $player->getWorld()->dropItem($player->getPosition(), $items);
                $player->sendMessage(str_replace(
                    ["{count}", "{item}", "{total}"],
                    [$amount, $items->getName(), $total],
                    $config->get("buy-message")
                ));
                return;
            }

            if(is_null($item->getSellPrice())){
                $player->sendMessage($config->get("not-sellable"));
                return;
            }

            $total = $amount * $item->getSellPrice();
            $items = $item->getItem()->setCount($amount);

            if(!$player->getInventory()->contains($items)){
                $player->sendMessage($config->get("not-enought-item"));
                return;
            }
            $moneyManager->addMoney($player->getName(), $total);
            $player->sendMessage(str_replace(
                ["{count}", "{item}", "{total}"],
                [$amount, $items->getName(), $total],
                $config->get("sell-message")
            ));
            $player->getInventory()->removeItem($items);

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