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

namespace Valres\CoreKitmap\managers\alt;

use JsonException;
use pocketmine\utils\Config;
use Valres\CoreKitmap\managers\BaseManager;

class AltAccountManager extends BaseManager
{
    /** @var AltInfos[] */
    private array $accounts = [];
    private Config $datas;

    public function getName(): string {
        return "Alt-Account";
    }

    public function load(): void {
        $this->datas = new Config($this->getPlugin()->getDataFolder() . "altAccounts/altAccounts.yml", Config::YAML);

        foreach($this->datas->getAll() as $playerName => $data){
            $this->accounts[$playerName] = new AltInfos(
                $data["ips"],
                $data["uuid"]
            );
        }
    }

    /** @throws JsonException */
    public function save(): void {
        foreach($this->accounts as $playerName => $data){
            $this->datas->set($playerName, [
                "ips" => $data->getIPs(),
                "uuid" => $data->getUuids()
            ]);
        }
        $this->datas->save();
    }

    public function exist(string $playerName): bool {
        return array_key_exists($playerName, $this->accounts);
    }

    public function register(string $name, string $ip, string $uuid): void {
        $this->accounts[$name] = new AltInfos([$ip], [$uuid]);
    }

    public function getIPs(string $playerName): ?array {
        return $this->accounts[$playerName]->getIPs() ?? null;
    }

    public function getUUIDs(string $playerName): ?array {
        return $this->accounts[$playerName]->getUuids() ?? null;
    }
}
