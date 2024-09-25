<?php

declare(strict_types=1);

namespace Valres\CoreKitmap\libs\invmenu\session\network\handler;

use Closure;
use Valres\CoreKitmap\libs\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}