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
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;

class EditcatShopCommand extends Command
{
    public function __construct() {
        parent::__construct("editcat-shop", "Modifier une catégorie du shop");
        $this->setPermission("editcat-shop.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) return;

        $form = new CustomForm(function(Player $player, array $data = null): void {
            if(is_null($data)) return;
            $shopManager = Core::getInstance()->shopManager;

            $categoryName = $shopManager->getNameCategory($data[0]);
            switch($data[1]){
                case 0:
                    $form = new CustomForm(function(Player $player, array $data = null) use ($shopManager, $categoryName): void {
                        if(is_null($data)) return;

                        $shopManager->changeCategoryName($categoryName, $data[0]);
                    });
                    $form->setTitle("Catégorie");
                    $form->addInput("Nouveau nom:");
                    $player->sendForm($form);
                    break;
                case 1:
                    $form = new CustomForm(function(Player $player, array $data = null) use ($shopManager, $categoryName): void {
                        if(is_null($data)) return;

                        $shopManager->changeCategoryItemDisplay($categoryName, $data[0]);
                    });
                    $form->setTitle("Catégorie");
                    $form->addInput("Nouvel item:");
                    $player->sendForm($form);
                    break;
                case 2:
                    $shopManager->removeCategory($categoryName);
                    break;
            }
        });
        $shopManager = Core::getInstance()->shopManager;

        $form->setTitle("Catégorie");
        $form->addDropdown("Catégorie à modifiée:", $shopManager->getCategories());
        $form->addDropdown("Action:", ["modifier le nom", "modifier l'item", "supprimer"]);
        $sender->sendForm($form);
    }
}