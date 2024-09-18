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

namespace Valres\CoreKitmap\commands\staff\combat;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\libs\jojoe77777\FormAPI\CustomForm;

class ChangeKnockbackCommand extends Command
{
    public function __construct() {
        parent::__construct("change-kb", "Change les knockback");
        $this->setPermission("change-kb.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) return;

        $this->sendForm($sender);
    }

    public function sendForm(Player $player, array $missings = [], array $errors = []): void {
        $form = new CustomForm(function(Player $player, array $data = null): void {
            if(is_null($data)) return;

            $errors   = [];
            $missings = [];

            $knockback      = $data[1];
            $attackCooldown = $data[2];

            if(!is_numeric($knockback))    $errors[] = "Les KB doivents être un chiffre à virgule.";
            if(!is_numeric($attackCooldown)) $errors[] = "L'attack-cooldown doit être un chiffre entier.";

            if($knockback === "")      $missings[] = "Knockback";
            if($attackCooldown === "") $missings[] = "Attack-cooldown";

            if(!empty($errors) or !empty($missings)){
                $this->sendForm($player, $missings, $errors);
                return;
            }

            $combatManager = Core::getInstance()->combatManager;
            $combatManager->setKnockback(floatval($knockback));
            $combatManager->setAttackCooldown(intval($attackCooldown));
        });
        $form->setTitle("Knockback");
        $content = "Veuillez remplir tout les champs.";
        if(!empty($missings)){
            $content .= "\n§cLes champs suivants sont manquants : " . implode(", ", $missings);
        }
        if(!empty($errors)){
            $content .= "\n§cErreurs :\n-" . implode("\n-", $errors);
        }
        $form->addLabel($content);
        $form->addInput("Knockback :", "Exemple : 0.37");
        $form->addInput("Attack-cooldown :", "Exemple : 8");
        $player->sendForm($form);
    }
}