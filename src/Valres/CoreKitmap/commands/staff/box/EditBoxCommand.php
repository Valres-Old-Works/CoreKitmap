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

namespace Valres\CoreKitmap\commands\staff\box;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\entity\BoxEntity;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\utils\Utils;

class EditBoxCommand extends Command
{
    public function __construct() {
        parent::__construct("edit-box", "Éditer une box", "usage : /edit-box <box-name>");
        $this->setPermission("edit-box.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::BOX);
        if(!$sender instanceof Player) return;

        if(count($args) < 1){
            $sender->sendMessage($this->getUsage());
            return;
        }

        $boxName = $args[0];
        $boxManager = Core::getInstance()->boxManager;

        if(!$boxManager->exist($boxName)){
            $sender->sendMessage($config->get("no-box"));
            return;
        }
        $box = $boxManager->getBox($boxName);

        //TODO: Edit box with form ui.
    }
}