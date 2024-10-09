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

namespace Valres\CoreKitmap\player;

use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\managers\combat\CombatInterface;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\managers\grades\Grade;
use Valres\CoreKitmap\managers\sanctions\CasierJudiciaire;

class CustomPlayer extends Player
{
    private Grade $grade;
    private PermissionAttachment $permissions;

    private CasierJudiciaire $casierJudiciaire;

    private Settings $settings;
    private CombatInterface $combatInterface;

    private bool $inFight = false;
    private int $fightTime = 0;

    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag) {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        $plugin = Core::getInstance();
        $gradeManager = $plugin->gradesManager;

        $this->grade            = $namedtag?->getTag("grade") instanceof StringTag ? $gradeManager->getGrade($namedtag->getString("grade")) : $gradeManager->getGrade(Core::getInstance()->getConfigFile(FilesManager::GRADES)->get("default-grade-identifier"));
        $this->calculPermissions();

        $this->casierJudiciaire = $namedtag?->getTag("casier") instanceof StringTag ? unserialize($namedtag->getString("casier")) : new CasierJudiciaire();
        $this->settings         = $namedtag?->getTag("settings") instanceof StringTag ? unserialize(($namedtag->getString("settings"))) : new Settings();
        $this->combatInterface  = new CombatInterface();
    }

    public function getSaveData(): CompoundTag {
        $nbt = parent::getSaveData();

        $nbt->setString("grade", $this->grade->getName());
        $nbt->setString("settings", serialize($this->settings));
        $nbt->setString("casier", serialize($this->casierJudiciaire));

        return $nbt;
    }

    public function isOp(): bool {
        return Server::getInstance()->isOp($this->getName());
    }

    public function getGrade(): Grade {
        return $this->grade;
    }

    public function setGrade(Grade $grade): void {
        $this->grade = $grade;
        $this->calculPermissions();
    }

    public function calculPermissions(): void {
        $this->permissions = $this->addAttachment(Core::getInstance());
        $permissions = [];
        foreach($this->getGrade()->getPermissions() as $perm){
            $permissions[$perm] = true;
        }
        $this->permissions->clearPermissions();
        $this->permissions->setPermissions($permissions);
    }

    public function getCasierJudiciaire(): CasierJudiciaire {
        return $this->casierJudiciaire;
    }

    public function getSettings(): Settings {
        return $this->settings;
    }

    public function getCombatInterface(): CombatInterface {
        return $this->combatInterface;
    }

    public function sendCombatInterface(): void {
        $tips = [];
        if($this->settings->isCps()){
            $tips[] = str_replace("{cps}", $this->combatInterface->getCps(), $this->combatInterface->getCpsFormat());
        }
        if($this->settings->isCombo()){
            $tips[] = str_replace("{combo}", $this->combatInterface->getCombos(), $this->combatInterface->getComboFormat());
        }
        if($this->settings->isReach()){
            $tips[] = str_replace("{reach}", $this->combatInterface->getReach(), $this->combatInterface->getReachFormat());
        }

        if(!empty($tips)){
            $this->sendTip(implode($this->combatInterface->getSeparator(), $tips));
        }
    }

    public function onUpdate(int $currentTick): bool {
        if($this->isInFight() and $this->fightTime <= time()) $this->updateFight(false);
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $source): void {
        parent::attack($source);
        if($source->isCancelled()) return;

        if($source instanceof EntityDamageByEntityEvent){
            $damager = $source->getDamager();
            if($damager instanceof $this){
                $this->updateFight();
                $damager->updateFight();
            }
        }
    }

    public function isInFight(): bool {
        return $this->inFight;
    }

    public function updateFight(bool $value = true): void {
        $config = Core::getInstance()->getConfigFile(FilesManager::COMBAT);
        if($value){
            if(!$this->isInFight()){
                $this->sendMessage($config->get("in-combat-message"));
                $this->inFight = true;
            }
            $this->fightTime = time() + Core::getInstance()->combatManager->getCombatTime();
        } else {
            $this->sendMessage($config->get("out-combat-message"));
            $this->inFight = false;
        }
    }

    protected function onDeath(): void {
        parent::onDeath();
        if($this->isInFight()){
            $this->updateFight(false);
        }
    }
}
