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

namespace Valres\CoreKitmap\managers\grades;

class Grade
{
    public function __construct(
        protected string $name,
        protected string $displayName,
        protected string $chatFormat,
        protected string $nametagFormat,
        protected string $color,
        protected array  $permissions
    ){}

    public function getName(): string {
        return $this->name;
    }

    public function getColoredName(): string {
        return $this->color . $this->displayName;
    }

    public function getDisplayName(): string {
        return $this->displayName;
    }

    public function getChatFormat(): string {
        return $this->chatFormat;
    }

    public function getNametagFormat(): string {
        return $this->nametagFormat;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }
}