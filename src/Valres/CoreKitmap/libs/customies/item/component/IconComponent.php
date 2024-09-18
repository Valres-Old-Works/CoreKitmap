<?php
declare(strict_types=1);

namespace Valres\CoreKitmap\libs\customies\item\component;

final class IconComponent implements ItemComponent {

	private string $texture;
    private ?string $iconTrim;

	public function __construct(string $texture, ?string $iconTrim = null) {
		$this->texture = $texture;
        $this->iconTrim = $iconTrim;
	}

	public function getName(): string {
		return "minecraft:icon";
	}

	public function getValue(): array {
		$array = [
			"textures" => [
				"default" => $this->texture
			]
		];
        if(!is_null($this->iconTrim)){
            $array["textures"]["icon_trim"] = $this->iconTrim;
        }
        return $array;
	}

	public function isProperty(): bool {
		return true;
	}
}
