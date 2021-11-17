<?php

namespace ImAMadDev\command\defaults;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\Tile;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\block\Block;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\command\Command;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnderChestCommand extends Command{
	
	public function __construct(){
		parent::__construct("echest", "Open your enderchest", "/echest", ["virtualchest", "ec"]);
	}
	
	public function execute(CommandSender $sender, string $label, array $args): void{
		if($sender instanceof HCFPlayer){
			$sender->setReplaceableBlock(RuntimeBlockMapping::getInstance()->toRuntimeId($sender->getWorld()->getBlock($sender->getPosition()->subtract(0, 1, 0))->getFullId()));
			$nbt = self::createTile($sender->getName());
            $sender->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create(BlockPosition::fromVector3($sender->getPosition()->subtract(0, 1, 0)), new CacheableNbt($nbt)));
            $this->send($sender);
			$sender->setCurrentWindow($sender->getEnderInventory());
		} else {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
		}
	}

    public function send(Player $player) : void{
        $player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(BlockPosition::fromVector3($player->getPosition()->subtract(0,1, 0)), RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::ENDER_CHEST()->getFullId()), UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL));
    }


    private static function createTile(string $playerName) : CompoundTag{
        $tag = CompoundTag::create()->setString(Tile::TAG_ID, "EnderChest");
        $tag->setString(Nameable::TAG_CUSTOM_NAME, $playerName . TextFormat::YELLOW . " Ender Chest");
        return $tag;
    }

}