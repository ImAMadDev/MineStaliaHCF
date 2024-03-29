<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\command\Command;
use ImAMadDev\manager\ReclaimManager;
use ImAMadDev\player\{PlayerData, HCFPlayer};

class ReclaimCommand extends Command {
	
	public function __construct() {
		parent::__construct("reclaim", "Reclaim command.", "/reclaim");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(empty(ReclaimManager::getReclaimByPlayer($sender))) {
			$sender->sendMessage(TextFormat::RED . "It appears there is no reclaim found for your rank!");
			return;
		}
		$time = (86400 - (time() - PlayerData::getCountdown($sender->getName(), 'reclaim')));
		if($time > 0) {
			$sender->sendMessage(TextFormat::RED . "You can't do /reclaim because you have a cooldown of " . gmdate('H:i:s', $time));
			return;
		}
		foreach(ReclaimManager::getReclaimByPlayer($sender) as $key) {
			if($sender->getInventory()->canAddItem($key)) {
				$sender->getInventory()->addItem($key);
			} else {
				$sender->getWorld()->dropItem($sender->getPosition()->asVector3(), $key, new Vector3(0, 0, 0));
			}
			$sender->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::DARK_RED . TextFormat::BOLD . $key->getCustomName());
		}
		PlayerData::setCountdown($sender->getName(), 'reclaim', (time() + 86400));
		$this->getServer()->broadcastMessage(TextFormat::colorize("&c{$sender->getName()} &7has reclaimed his keys from the &e/reclaim"));
	}
}