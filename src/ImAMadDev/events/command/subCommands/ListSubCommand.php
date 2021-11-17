<?php

namespace ImAMadDev\events\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\HCF;
use ImAMadDev\command\SubCommand;

class ListSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("list", "/event list");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$sender->sendMessage(HCF::$EventsManager->getEventsList());
	}
}