<?php

namespace Valres\CoreKitmap\listeners;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use Valres\CoreKitmap\libs\customies\block\CustomiesBlockFactory;
use Valres\CoreKitmap\libs\customies\item\CustomiesItemFactory;
use function array_merge;
use function count;

final class CustomiesListener implements Listener 
{
	private ?ItemComponentPacket $cachedItemComponentPacket = null;
	/** @var ItemTypeEntry[] */
	private array $cachedItemTable = [];
	/** @var BlockPaletteEntry[] */
	private array $cachedBlockPalette = [];
	private Experiments $experiments;

	public function __construct() {
		$this->experiments = new Experiments([
			"data_driven_items" => true,
		], true);
	}

	public function onDataPacketSend(DataPacketSendEvent $event): void {
		foreach($event->getPackets() as $packet){
			if($packet instanceof BiomeDefinitionListPacket) {
				if($this->cachedItemComponentPacket === null) {
					$this->cachedItemComponentPacket = ItemComponentPacket::create(CustomiesItemFactory::getInstance()->getItemComponentEntries());
				}
				foreach($event->getTargets() as $session){
					$session->sendDataPacket($this->cachedItemComponentPacket);
				}
			} elseif($packet instanceof StartGamePacket) {
				if(count($this->cachedItemTable) === 0) {
					$this->cachedItemTable = CustomiesItemFactory::getInstance()->getItemTableEntries();
					$this->cachedBlockPalette = CustomiesBlockFactory::getInstance()->getBlockPaletteEntries();
				}
				$packet->levelSettings->experiments = $this->experiments;
				$packet->itemTable = array_merge($packet->itemTable, $this->cachedItemTable);
				$packet->blockPalette = $this->cachedBlockPalette;
			} elseif($packet instanceof ResourcePackStackPacket) {
				$packet->experiments = $this->experiments;
			}
		}
	}
}
