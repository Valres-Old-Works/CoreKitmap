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

use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;

class AddcatShopCommand extends Command
{
    public function __construct() {
        parent::__construct("addcat-shop", "Ajoute une catégorie dans le shop");
        $this->setPermission("addcat-shop.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) return;

        $form = new CustomForm(function(Player $player, array $data = null): void {
            if(is_null($data)) return;

            $name = $data[0];

            Core::getInstance()->shopManager->addCategory($name, $data[1] ?? "dirt");
        });
        $form->setTitle("Catégorie");
        $form->addInput("Nom :");
        $form->addInput("Item :");
        $sender->sendForm($form);
    }
}