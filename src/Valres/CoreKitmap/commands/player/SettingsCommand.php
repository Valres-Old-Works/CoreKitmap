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

namespace Valres\CoreKitmap\commands\player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;
use Valres\CoreKitmap\player\CustomPlayer;

class SettingsCommand extends Command
{
    public function __construct() {
        parent::__construct("settings", "Ouvre les paramètres");
        $this->setPermission("settings.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CustomPlayer) return;

        $form = new CustomForm(function(CustomPlayer $player, array $data = null): void {
            if(is_null($data)) return;

            $player->getSettings()->setScoreboard($data[0]);
            $player->getSettings()->setCps($data[1]);
            $player->getSettings()->setCombo($data[2]);
            $player->getSettings()->setReach($data[3]);
            $player->getSettings()->setSprint($data[4]);
            $player->getSettings()->setMp($data[5]);
        });
        $settings = $sender->getSettings();
        $form->setTitle("Paramètres");
        $form->addToggle("Scoreboard", $settings->isScoreboard());
        $form->addToggle("CPS", $settings->isCps());
        $form->addToggle("Combo", $settings->isCombo());
        $form->addToggle("Reach", $settings->isReach());
        $form->addToggle("ToogleSprint", $settings->isSprint());
        $form->addToggle("MP", $settings->isMp());
        $sender->sendForm($form);
    }
}