<?php

declare(strict_types=1);

namespace Valres\CoreKitmap\libs\invmenu\type;

use Valres\CoreKitmap\libs\invmenu\InvMenu;
use Valres\CoreKitmap\libs\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}