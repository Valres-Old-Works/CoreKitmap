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

namespace Valres\CoreKitmap\commands\staff\shop;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;
use Valres\CoreKitmap\managers\shop\ShopItem;

class EdititemShopCommand extends Command
{
    public function __construct() {
        parent::__construct("edititem-shop", "Modifier un item du shop");
        $this->setPermission("edititem-shop.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) return;

        $form = new CustomForm(function(Player $player, array $data = null): void {
            if(is_null($data)) return;
            $shopManager = Core::getInstance()->shopManager;

            $categoryName = $shopManager->getNameCategory($data[0]);
            $form = new CustomForm(function(Player $player, array $data = null) use ($shopManager, $categoryName): void {
                if(is_null($data)) return;

                $index = $data[0];
                switch($data[1]){
                    case 0:
                        $form = new CustomForm(function(Player $player, array $data = null) use ($shopManager, $categoryName, $index): void {
                            if(is_null($data)) return;

                            $shopItem = $shopManager->getShopItem($categoryName, $index);
                            $shopManager->modifyShopItem($categoryName, $index, new ShopItem(
                                StringToItemParser::getInstance()->lookupAliases($shopItem->getItem())[0],
                                ($data[0] === "" ? null : floatval($data[0])),
                                ($data[1] === "" ? null : floatval($data[1]))
                            ));
                        });
                        $form->setTitle("Item");
                        $form->addInput("Prix de d'achat:");
                        $form->addInput("Prix de vente:");
                        $player->sendForm($form);
                        break;
                    case 1:
                        $shopManager->removeShopItem($categoryName, $index);
                        break;
                }
            });
            $form->setTitle("Item");
            $form->addDropdown("Item à modifier:", array_map(function(ShopItem $shopItem): string {
                return $shopItem->getItem()->getName();
            }, $shopManager->getShopItems($categoryName)));
            $form->addDropdown("Action:", ["modifier", "supprimer"]);
            $player->sendForm($form);
        });
        $shopManager = Core::getInstance()->shopManager;

        $form->setTitle("Item");
        $form->addDropdown("Catégorie ou se trouve l'item:", $shopManager->getCategories());
        $sender->sendForm($form);
    }
}