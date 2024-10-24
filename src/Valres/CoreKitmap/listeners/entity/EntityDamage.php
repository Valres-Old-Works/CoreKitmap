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

namespace Valres\CoreKitmap\listeners\entity;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\player\CustomPlayer;

class EntityDamage implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void {
        $combatManager = Core::getInstance()->combatManager;
        $victim        = $event->getEntity();
        $damager       = $event->getDamager();

        if(!$victim instanceof CustomPlayer or !$damager instanceof CustomPlayer) return;

        $event->setAttackCooldown($combatManager->getAttackCooldown());
        $event->setKnockBack($combatManager->getKnockback());

        $damager->updateFight();
        $victim->updateFight();

        $damager->getCombatInterface()->addCombo();
        $victim->getCombatInterface()->resetCombo();

        $distance = $damager->getPosition()->distance($victim->getPosition());
        $damager->getCombatInterface()->setReach(min($distance, 3.0));

        $damager->sendCombatInterface();
    }

    public function _onDamage(EntityDamageEvent $event): void {
        $cause  = $event->getCause();
        $entity = $event->getEntity();

        if(!$entity instanceof CustomPlayer) return;

        if($cause === EntityDamageEvent::CAUSE_VOID){
            $entity->teleport($entity->getWorld()->getSafeSpawn());
            $event->cancel();
            return;
        }

        if($cause === EntityDamageEvent::CAUSE_FALL){
            $event->cancel();
            return;
        }
    }
}