<?php

declare(strict_types=1);

namespace Valres\CoreKitmap\libs\invmenu\session;

use Valres\CoreKitmap\libs\invmenu\InvMenu;
use Valres\CoreKitmap\libs\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}