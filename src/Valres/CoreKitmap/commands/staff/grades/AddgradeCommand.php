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

namespace Valres\CoreKitmap\commands\staff\grades;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;
use Valres\CoreKitmap\managers\grades\Grade;

class AddgradeCommand extends Command
{
    public function __construct() {
        parent::__construct("addgrade", "Créer un grade", "usage : /addgrade");
        $this->setPermission("addgrade.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) return;

        $this->sendForm($sender);
    }

    public function sendForm(Player $player, array $missings = []): void {
        $form = new CustomForm(function(Player $player, array $data = null): void {
            if(is_null($data)) return;

            $missings = [];

            $id = $data[1];
            $name = $data[2];
            $chatFormat = $data[3];
            $nametagFormat = $data[4];
            $color = $data[5];

            if ($id === "") $missings[] = "Identifiant";
            if ($name === "") $missings[] = "Nom";
            if ($chatFormat === "") $missings[] = "Format dans le chat";
            if ($nametagFormat === "") $missings[] = "Format du nametag";
            if ($color === "") $missings[] = "Couleur";

            if(!empty($missings)){
                $this->sendForm($player, $missings);
                return;
            }

            Core::getInstance()->gradesManager->addGrade(new Grade($id, $name, $chatFormat, $nametagFormat, $color, []));
        });
        $form->setTitle("Grade > Add");
        $content = "Veuillez remplir tout les champs.";
        if(!empty($missings)){
            $content .= "\n§cLes champs suivants sont manquants :\n" . implode("\n- ", $missings);
        }
        $form->addLabel($content);
        $form->addInput("Identifiant :", "Exemple: joueur");
        $form->addInput("Nom :", "Exemple: Joueur");
        $form->addInput("Format dans le chat :");
        $form->addInput("Format du nametag :");
        $form->addInput("Couleur :", "Exemple: \§4");
        $player->sendForm($form);
    }
}