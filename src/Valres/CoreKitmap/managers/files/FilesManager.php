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

namespace Valres\CoreKitmap\managers\files;

use Valres\CoreKitmap\managers\BaseManager;

class FilesManager extends BaseManager
{
    const COMBAT    = "configs/combat-config";
    const BOX       = "configs/box-config";
    const GRADES    = "configs/grades-config";
    const SANCTIONS = "configs/sanctions-config";
    const MONEY     = "configs/money-config";
    const STATS     = "configs/statistics-config";

    public function getName(): string {
        return "Files";
    }

    public function load(): void {
        $files = [
            "sanctions/bans", "sanctions/IPBans", "sanctions/uuidBans", "sanctions/mutes", "sanctions/blacklist", "configs/sanctions-config",
            "altAccounts/altAccounts",
            "grades/grades", "configs/grades-config", "configs/combat-config",
            "moneys/moneys", "configs/money-config",
            "statistics/statistics", "configs/statistics-config",
            "box/box", "configs/box-config"
        ];

        $dirs = [
            "box/textures/", "box/geometries/",
        ];

        foreach($files as $file){
            $this->getPlugin()->saveResource($file . ".yml");
        }

        foreach($dirs as $dir){
            @mkdir($this->getPlugin()->getDataFolder() . $dir);
        }
    }

    public function save(): void {}
}
