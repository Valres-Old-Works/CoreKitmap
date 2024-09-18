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

namespace Valres\CoreKitmap\managers\sanctions;

use JsonException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\utils\Config;
use Valres\CoreKitmap\libs\AtomDiscordAPI\Embed;
use Valres\CoreKitmap\libs\AtomDiscordAPI\Message;
use Valres\CoreKitmap\libs\AtomDiscordAPI\Webhook;
use Valres\CoreKitmap\managers\BaseManager;
use Valres\CoreKitmap\managers\sanctions\types\Ban;
use Valres\CoreKitmap\managers\sanctions\types\IPBan;
use Valres\CoreKitmap\managers\sanctions\types\Mute;
use Valres\CoreKitmap\managers\sanctions\types\UuidBan;
use Valres\CoreKitmap\player\CustomPlayer;
use Valres\CoreKitmap\utils\TimeHelper;

class SanctionsManager extends BaseManager
{
    /** @var Ban[] */
    private array $bans = [];
    /** @var IPBan[] */
    private array $ipBans = [];
    /** @var UuidBan[] */
    private array $uuidBans = [];
    /** @var Mute[] */
    private array $mutes = [];

    /** @var string[] */
    private array $blacklists = [];

    private Config $bansData;
    private Config $IPBansData;
    private Config $uuidBansData;
    private Config $mutesData;
    private Config $blacklistsData;
    private Config $config;

    private Webhook $webhook;

    public function getName(): string {
        return "Sanction";
    }

    /** @throws JsonException */
    public function load(): void {
        $this->init();

        $this->webhook = new Webhook($this->config->get("webhook-url"));

        foreach($this->bansData->getAll() as $playerName => $data){
            if($data["time"] > time()){
                $this->bans[$playerName] = new Ban($playerName, $data["reason"], $data["time"], $data["author"]);
            }
        }
        $this->bansData->setAll([]);
        $this->bansData->save();

        foreach($this->IPBansData->getAll() as $ip => $data){
            if($data["time"] > time()){
                $this->ipBans[$ip] = new IPBan($data["name"], $ip, $data["reason"], $data["time"], $data["author"]);
            }
        }
        $this->IPBansData->setAll([]);
        $this->IPBansData->save();

        foreach($this->uuidBansData->getAll() as $uuid => $data){
            if($data["time"] > time()){
                $this->uuidBans[$uuid] = new UuidBan($data["name"], $uuid, $data["reason"], $data["time"], $data["author"]);
            }
        }
        $this->uuidBansData->setAll([]);
        $this->uuidBansData->save();

        foreach($this->mutesData->getAll() as $playerName => $data){
            if($data["time"] > time()){
                $this->mutes[$playerName] = new Mute($playerName, $data["reason"], $data["time"], $data["author"]);
            }
        }
        $this->mutesData->setAll([]);
        $this->mutesData->save();

        foreach($this->blacklistsData->getAll() as $data){
            $this->blacklists[] = $data;
        }
        $this->blacklistsData->setAll([]);
        $this->blacklistsData->save();
    }

    /** @throws JsonException */
    public function save(): void {
        foreach($this->bans as $playerName => $ban) {
            $this->bansData->set($playerName, [
                "reason" => $ban->getReason(),
                "time" => $ban->getTime(),
                "author" => $ban->getAuthorName()
            ]);
        }

        foreach($this->ipBans as $ip => $ban){
            $this->IPBansData->set($ip, [
                "name"   => $ban->getPlayerName(),
                "reason" => $ban->getReason(),
                "time"   => $ban->getTime(),
                "author" => $ban->getAuthorName()
            ]);
        }

        foreach($this->uuidBans as $uuid => $ban){
            $this->uuidBansData->set($uuid, [
                "name"   => $ban->getPlayerName(),
                "reason" => $ban->getReason(),
                "time"   => $ban->getTime(),
                "author" => $ban->getAuthorName()
            ]);
        }

        foreach($this->mutes as $playerName => $mute){
            $this->mutesData->set($playerName, [
                "reason" => $mute->getReason(),
                "time"   => $mute->getTime(),
                "author" => $mute->getAuthorName()
            ]);
        }

        foreach($this->blacklists as $data){
            $this->blacklistsData->set($data);
        }

        $this->bansData->save();
        $this->IPBansData->save();
        $this->uuidBansData->save();
        $this->mutesData->save();
        $this->blacklistsData->save();
    }

    private function init(): void {
        $plugin = $this->getPlugin();

        $this->config         = new Config($plugin->getDataFolder() . "sanctions-config.yml", Config::YAML);
        $this->bansData       = new Config($plugin->getDataFolder() . "sanctions/bans.yml", Config::YAML);
        $this->IPBansData     = new Config($plugin->getDataFolder() . "sanctions/IPBans.yml", Config::YAML);
        $this->uuidBansData   = new Config($plugin->getDataFolder() . "sanctions/uuidBans.yml", Config::YAML);
        $this->blacklistsData = new Config($plugin->getDataFolder() . "sanctions/blacklists.yml", Config::YAML);
        $this->mutesData      = new Config($plugin->getDataFolder() . "sanctions/mutes.yml", Config::YAML);
    }

    public function getBans(): array {
        return $this->bans + $this->ipBans + $this->uuidBans;
    }

    public function getBan(string $playerName): ?Ban {
        return $this->bans[$playerName] ?? null;
    }

    public function getIPBan(string $ip): ?IPBan {
        return $this->ipBans[$ip] ?? null;
    }

    public function getUuidBan(string $uuid): ?UuidBan {
        return $this->uuidBans[$uuid] ?? null;
    }

    public function isBanned(string $playerName): bool {
        return array_key_exists($playerName, $this->bans);
    }

    public function isIPBanned(string $ip): bool {
        return array_key_exists($ip, $this->ipBans);
    }

    public function isUuidBanned(string $uuid): bool {
        return array_key_exists($uuid, $this->uuidBans);
    }

    public function addBan(Ban $ban, bool $new = false): void {
        $this->bans[$ban->getPlayerName()] = $ban;
        if($new){
            $this->addBanToCasier($ban->getPlayerName(), $ban);
            $this->webhook->send((new Message())
                ->addEmbed((new Embed())
                    ->setTitle($this->config->get("ban-embed")["title"])
                    ->setDescription(str_replace(
                        ["{player}", "{reason}", "{time}"],
                        [$ban->getPlayerName(), $ban->getReason(), TimeHelper::timeToString($ban->getTime())],
                        $this->config->get("ban-embed")["description"]
                    ))
                    ->setFooter(str_replace(
                        "{author}",
                        $ban->getAuthorName(),
                        $this->config->get("ban-embed")["footer"]
                    ))
                )
            );
            Server::getInstance()->broadcastMessage(str_replace(
                ["{player}", "{reason}", "{time}", "{author}"],
                [$ban->getPlayerName(), $ban->getReason(), TimeHelper::timeToString($ban->getTime()), $ban->getAuthorName()],
                $this->config->get("ban-message")
            ));
        }
    }

    public function addIPBan(IPBan $IPBan): void {
        $this->ipBans[$IPBan->getIP()] = $IPBan;
    }

    public function addUuidBan(UuidBan $uuidBan): void {
        $this->uuidBans[$uuidBan->getUuid()] = $uuidBan;
    }

    public function addBanToCasier(string $player, Ban $ban): void {
        $player_ = Server::getInstance()->getPlayerExact($player);
        if(!$player_ instanceof CustomPlayer){
            $offlinedata = Server::getInstance()->getOfflinePlayerData($player);
            if(!$offlinedata instanceof CompoundTag){
                return;
            }
            /** @var CasierJudiciaire $casier */
            $casier = unserialize($offlinedata->getString("casier"));
            $casier->addBan($ban);
            $offlinedata->setString("casier", serialize($casier));
            Server::getInstance()->saveOfflinePlayerData($player, $offlinedata);
        } else $player_->getCasierJudiciaire()->addBan($ban);
    }

    public function removeBan(string $playerName): void {
        unset($this->bans[$playerName]);
    }

    public function removeIPBan(string $ip): void {
        unset($this->ipBans[$ip]);
    }

    public function removeUuidBan(string $uuid): void {
        unset($this->uuidBans[$uuid]);
    }

    public function getMutes(): array {
        return $this->mutes;
    }

    public function getMute(string $playerName): ?Mute {
        return $this->mutes[$playerName] ?? null;
    }

    public function isMuted(string $playerName): bool {
        return array_key_exists($playerName, $this->mutes);
    }

    public function addMute(Mute $mute, bool $new): void {
        $this->mutes[$mute->getPlayerName()] = $mute;
        if($new){
            $this->addMuteToCasier($mute->getPlayerName(), $mute);
            $this->webhook->send((new Message())
                ->addEmbed((new Embed())
                    ->setTitle($this->config->get("mute-embed")["title"])
                    ->setDescription(str_replace(
                        ["{player}", "{reason}", "{time}"],
                        [$mute->getPlayerName(), $mute->getReason(), TimeHelper::timeToString($mute->getTime())],
                        $this->config->get("mute-embed")["description"]
                    ))
                    ->setFooter(str_replace(
                        "{author}",
                        $mute->getAuthorName(),
                        $this->config->get("mute-embed")["footer"]
                    ))
                )
            );
            Server::getInstance()->broadcastMessage(str_replace(
                ["{player}", "{reason}", "{time}", "{author}"],
                [$mute->getPlayerName(), $mute->getReason(), TimeHelper::timeToString($mute->getTime()), $mute->getAuthorName()],
                $this->config->get("ban-message")
            ));
        }
    }

    public function addMuteToCasier(string $player, Mute $mute): void {
        $player_ = Server::getInstance()->getPlayerExact($player);
        if(!$player_ instanceof CustomPlayer){
            $offlinedata = Server::getInstance()->getOfflinePlayerData($player);
            /** @var CasierJudiciaire $casier */
            $casier = unserialize($offlinedata->getString("casier"));
            $casier->addMute($mute);
            $offlinedata->setString("casier", serialize($casier));
            Server::getInstance()->saveOfflinePlayerData($player, $offlinedata);
        } else $player_->getCasierJudiciaire()->addMute($mute);
    }

    public function removeMute(string $playerName): void {
        unset($this->mutes[$playerName]);
        Server::getInstance()->broadcastMessage(str_replace(
            "{player}",
            $playerName,
            $this->config->get("unmute-message")
        ));
    }

    public function isBlacklist(string ...$value): bool {
        return in_array($value, $this->blacklists);
    }

    public function addToBlacklist(string $value): void {
        $this->blacklists[] = $value;
    }
}