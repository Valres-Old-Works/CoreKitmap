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

namespace Valres\CoreKitmap\managers\combat;

use JsonException;
use pocketmine\utils\Config;
use Valres\CoreKitmap\managers\BaseManager;

class StatisticsManager extends BaseManager
{
    /** @var Statistics[] */
    private array $stats = [];

    private Config $datas;


    public function getName(): string {
        return "Statistics";
    }

    public function load(): void {
        $this->datas = new Config($this->getPlugin()->getDataFolder() . "statistics/statistics.yml", Config::YAML);

        foreach($this->datas->getAll() as $playerName => $data){
            $this->stats[$playerName] = new Statistics($data["kills"], $data["deaths"]);
        }
    }

    /** @throws JsonException */
    public function save(): void {
        foreach($this->stats as $playerName => $data){
            $this->datas->set($playerName, [
                "kills"  => $data->getKills(),
                "deaths" => $data->getDeath()
            ]);
        }
        $this->datas->save();
    }

    public function getStats(string $playerName): ?Statistics {
        return $this->stats[$playerName] ?? null;
    }

    public function getKills(): array {
        return array_map(function(Statistics $stats): int {
            return $stats->getKills();
        }, $this->stats);
    }

    public function getDeaths(): array {
        return array_map(function(Statistics $stats): int {
            return $stats->getDeath();
        }, $this->stats);
    }

    public function getKDR(): array {
        return array_map(function(Statistics $stats): float {
            return $stats->getKDR();
        }, $this->stats);
    }

    public function exist(string $playerName): bool {
        return array_key_exists($playerName, $this->stats);
    }

    public function register(string $playerName): void {
        $this->stats[$playerName] = new Statistics();
    }
}