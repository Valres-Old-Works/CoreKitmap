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

namespace Valres\CoreKitmap\managers\credit;

use JsonException;
use pocketmine\utils\Config;
use Valres\CoreKitmap\managers\BaseManager;
use Valres\CoreKitmap\managers\files\FilesManager;

class CreditManager extends BaseManager
{
    /** @var int[] */
    private array $credits = [];
    private Config $datas;

    private Config $config;

    public function getName(): string {
        return "Crédit";
    }

    public function load(): void {
        $this->datas  = new Config($this->getPlugin()->getDataFolder() . "credits/credits.yml");
        $this->config = $this->getPlugin()->getConfigFile(FilesManager::MONEY);

        foreach($this->datas->getAll() as $playerName => $money){
            $this->credits[$playerName] = $money;
        }
    }

    /** @throws JsonException */
    public function save(): void {
        foreach($this->credits as $playerName => $money){
            $this->datas->set($playerName, $money);
        }
        $this->datas->save();
    }

    public function exist(string $playerName): bool {
        return array_key_exists($playerName, $this->credits);
    }

    public function register(string $playerName): void {
        $this->credits[$playerName] = $this->config->get("default-money");
    }

    public function getCredits(): array {
        return $this->credits;
    }

    public function getCredit(string $playerName): ?float {
        return $this->credits[$playerName] ?? null;
    }

    public function addCredit(string $playerName, float $values): void {
        $this->credits[$playerName] += $values;
    }

    public function reduceCredit(string $playerName, float $values): void {
        $this->credits[$playerName] -= $values;
    }

    public function setCredit(string $playerName, float $values): void {
        $this->credits[$playerName] = $values;
    }
}