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

namespace Valres\CoreKitmap\utils;

class TimeHelper
{
    public static array $timeUnits = [
        "y" => 31536000,
        "M" => 2635200,
        "w" => 604800,
        "d" => 86400,
        "h" => 3600,
        "m" => 60,
        "s" => 1
    ];

    public static function stringToTime(string $timeString): int {
        $totalSeconds = 0;
        $matches = [];

        preg_match_all('/(\d+)([yMwdhms])/', $timeString, $matches, PREG_SET_ORDER);

        foreach($matches as $match){
            $quantity = intval($match[1]);
            $unit = $match[2];
            if(isset(self::$timeUnits[$unit])){
                $totalSeconds += $quantity * self::$timeUnits[$unit];
            }
        }

        return time() + $totalSeconds;
    }

    public static function timeToString(int $time): string {
        $timeRestant = $time - time();
        if($timeRestant < 0) return "0s";

        $formatTemp = '';

        foreach(self::$timeUnits as $unit => $value){
            if($timeRestant >= $value){
                $quantity = intval($timeRestant / $value);
                $formatTemp .= $quantity . $unit . ' ';
                $timeRestant -= $quantity * $value;
            }
        }

        return trim($formatTemp);
    }

    public static function timestampToDate(int $timestamp, string $format = 'Y-m-d H:i:s'): string {
        return date($format, $timestamp);
    }
}
