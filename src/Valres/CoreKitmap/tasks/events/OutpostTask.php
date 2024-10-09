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

namespace Valres\CoreKitmap\tasks\events;

use pocketmine\entity\Location;
use pocketmine\math\AxisAlignedBB;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Valres\CoreKitmap\Core;
use Valres\CoreKitmap\entity\events\OutpostFloatingText;
use Valres\CoreKitmap\managers\events\types\Outpost;

class OutpostTask extends Task
{
    public Outpost $outpost;
    public OutpostFloatingText $floatingText;

    private AxisAlignedBB $zone;

    public function __construct(Outpost $outpost, AxisAlignedBB $zone) {
        $this->outpost = $outpost;
        $this->zone = $zone;

        $this->floatingText = (new OutpostFloatingText(new Location(411.5, 66, 256.5, $outpost->getWorld(), 0, 0)))->setOutpost($this->getOutpost());
        $this->floatingText->spawnToAll();
        Core::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function getOutpost(): Outpost {
        return $this->outpost;
    }

    public function onRun(): void {
        $outpost = $this->outpost;

        $factions = [];
        $zone = $this->zone;


        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if($zone->isVectorInside($player->getPosition())){
                if(!is_null($player->getFaction())){
                    if(!in_array($player->getFactionName(), $factions)){
                        $factions[] = $player->getFactionName();
                    }
                }
            }
        }

        $conquered = !is_null($outpost->getFaction());
        $captureTime = $outpost->getCapturedTime();

        if($conquered){
            if(!is_null($outpost->getRewardTimeRemaining()) && $outpost->getRewardTimeRemaining() <= 0) {
                Server::getInstance()->broadcastMessage("La faction §t" . $outpost->getFaction()->getName() . "§r vient de recevoir une récompense grace à l'avant-poste !");
                $outpost->giveReward();
                $outpost->setRewardTime(time());
            }
        }

        if(empty($factions)){
            $outpost->setCapturedTime(0);
            $outpost->setCaptureFaction(null);
        } elseif(count($factions) === 1) {
            $faction = array_shift($factions);
            if(!$conquered || $faction !== $outpost->getFaction()->getName()) {
                if(is_null($outpost->getCaptureFaction()) || $faction !== $outpost->getCaptureFaction()) {
                    Server::getInstance()->broadcastMessage("La faction §t" . $faction . "§r est en train de capturer l'avant-poste.");
                    $outpost->setCaptureFaction($faction);
                    $outpost->setCapturedTime(0);
                }
                if($conquered) {
                    if($captureTime === 0) {
                        $outpost->setCapturedTime(1);
                        Server::getInstance()->broadcastMessage("La faction §t" . $faction . "§r est entrain de récupérer l’avant-poste de la faction §t" . $outpost->getFaction()->getName() . "§r !");
                    }elseif($captureTime < 60) {
                        $outpost->setCapturedTime($captureTime + 1);
                    }else{
                        Server::getInstance()->broadcastMessage("La faction §t" . $outpost->getFaction()->getName() . "§r vient de perdre l'avant-poste");
                        $outpost->setCapturedTime(0);
                        $outpost->setFaction(null);
                    }
                }else{
                    if($captureTime < 180) {
                        if(!is_null($outpost->getRewardTimeRemaining())) {
                            $outpost->setRewardTime(null);
                        }
                        $outpost->setCapturedTime($captureTime + 1);
                    }else{
                        $outpost->setCapturedTime(0);
                        $outpost->setCaptureFaction(null);
                        $outpost->setFaction($faction);
                        $outpost->setRewardTime(time());
                        Server::getInstance()->broadcastMessage("La faction §t" . $faction . "§r vient de capturer l'avant-poste !");
                    }
                }
            }else{
                $outpost->setCaptureFaction(null);
                $outpost->setCapturedTime(0);
            }
        }else{
            if(in_array($outpost->getCaptureFaction(), $factions)) {
                $faction = $outpost->getCaptureFaction();
                if($conquered) {
                    if($captureTime < 60) {
                        $outpost->setCapturedTime($captureTime + 1);
                    }else{
                        Server::getInstance()->broadcastMessage("La faction §t" . $outpost->getFaction()->getName() . "§r vient de perdre l'avant-poste");
                        $outpost->setCapturedTime(0);
                        $outpost->setFaction(null);
                    }
                }else{
                    if($captureTime < 180) {
                        $outpost->setCapturedTime($captureTime + 1);
                    }else{
                        $outpost->setCapturedTime(0);
                        $outpost->setCaptureFaction(null);
                        $outpost->setFaction($faction);
                        $outpost->setRewardTime(time());
                        Server::getInstance()->broadcastMessage("La faction §t" . $faction . "§r vient de capturer l'avant-poste !");
                    }
                }
            }else{
                if($captureTime > 0) {
                    $outpost->setCapturedTime(0);
                    $outpost->setCaptureFaction(null);
                }
            }
        }
    }
}