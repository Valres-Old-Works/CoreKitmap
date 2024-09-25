<?php

declare(strict_types=1);

namespace Valres\CoreKitmap\libs\invmenu\type\graphic\network;

use Valres\CoreKitmap\libs\invmenu\session\InvMenuInfo;
use Valres\CoreKitmap\libs\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}