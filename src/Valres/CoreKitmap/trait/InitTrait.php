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

namespace Valres\CoreKitmap\trait;

use Valres\CoreKitmap\managers\alt\AltAccountManager;
use Valres\CoreKitmap\managers\box\BoxManager;
use Valres\CoreKitmap\managers\combat\CombatManager;
use Valres\CoreKitmap\managers\combat\statisctics\StatisticsManager;
use Valres\CoreKitmap\managers\commands\CommandsManager;
use Valres\CoreKitmap\managers\files\FilesManager;
use Valres\CoreKitmap\managers\grades\GradesManager;
use Valres\CoreKitmap\managers\listeners\ListenersManager;
use Valres\CoreKitmap\managers\money\MoneyManager;
use Valres\CoreKitmap\managers\sanctions\SanctionsManager;

trait InitTrait
{
    public AltAccountManager $accountManager;
    public CombatManager     $combatManager;
    public BoxManager        $boxManager;
    public CommandsManager   $commandsManager;
    public FilesManager      $filesManager;
    public GradesManager     $gradesManager;
    public ListenersManager  $listenersManager;
    public MoneyManager      $moneyManager;
    public SanctionsManager  $sanctionsManager;
    public StatisticsManager $statisticsManager;

    public function initAll(): void {
        $this->filesManager      = new FilesManager();
        $this->accountManager    = new AltAccountManager();
        $this->boxManager        = new BoxManager();
        $this->combatManager     = new CombatManager();
        $this->commandsManager   = new CommandsManager();
        $this->gradesManager     = new GradesManager();
        $this->listenersManager  = new ListenersManager();
        $this->moneyManager      = new MoneyManager();
        $this->sanctionsManager  = new SanctionsManager();
        $this->statisticsManager = new StatisticsManager();
    }
}
