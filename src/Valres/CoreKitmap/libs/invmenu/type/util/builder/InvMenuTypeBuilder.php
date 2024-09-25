<?php

declare(strict_types=1);

namespace Valres\CoreKitmap\libs\invmenu\type\util\builder;

use Valres\CoreKitmap\libs\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}