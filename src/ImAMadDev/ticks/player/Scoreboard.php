<?php

namespace ImAMadDev\ticks\player;

use ImAMadDev\faction\FactionRally;
use ImAMadDev\HCF;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\{PlayerUtils, HCFPlayer};

use ImAMadDev\manager\{ClaimManager, EventsManager, SOTWManager, KOTHManager, EOTWManager};

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class Scoreboard extends Task {
	
	protected HCFPlayer $player;
	
	public function __construct(HCFPlayer $player){
		$this->player = $player;
	}
	
	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
			$this->getHandler()->cancel();
			return;
		}
		$player->checkInvisibility();
		if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false) $player->getHungerManager()->setFood(20);
		$api = HCF::getScoreboard();
		$scoreboard = [];
		if(count(array_keys($player->getCooldowns())) >= 1) {
			foreach(array_keys($player->getCooldowns()) as $name) {
				if($player->getCooldown()->has($name)) {
					$tag = PlayerUtils::$cooldownsNames[$name] ?? $name;
					$scoreboard[] = TextFormat::colorize($tag) . gmdate('i:s', $player->getCooldown()->get($name));
				}
			}
		}
		if(KOTHManager::getInstance()->getSelected() !== null) {
			$scoreboard[] = TextFormat::DARK_RED . KOTHManager::getInstance()->getSelected()->getName() . ": " . gmdate('i:s', KOTHManager::getInstance()->getSelected()->getTime());
		}
		if(!empty(EventsManager::getInstance()->getEvents())) {
			foreach(EventsManager::getInstance()->getEvents() as $event) {
				$scoreboard[] = TextFormat::DARK_BLUE . $event->getScoreboard() . ": " . gmdate('i:s', $event->getTime());
			}
		}
		if($player->isInvincible()){
			$scoreboard[] = TextFormat::colorize("&ePVP Timer: ") . gmdate('H:i:s', $player->getInvincibilityTime());
		}
		if(EOTWManager::isEnabled()){
			$scoreboard[] = TextFormat::colorize("&cEndOfTheWorld: ") . gmdate("H:i:s", EOTWManager::getTime());
		}
		if(SOTWManager::isEnabled()){
			$scoreboard[] = TextFormat::colorize("&aStartOfTheWorld: ") . gmdate("H:i:s", SOTWManager::getTime());
		}
		if($player->isBard()) {
			$scoreboard[] = TextFormat::colorize("&6Bard Energy: ") . $player->getBardEnergy();
		}
		if($player->isMage()) {
			$scoreboard[] = TextFormat::colorize("&eMage Energy: ") . $player->getBardEnergy();
		}
		if($player->getFocus() !== null && $player->getFocus()->getFaction() instanceof Faction) {
			if(HCF::$factionManager->isFaction($player->getFocus()->getFaction()->getName())) {
				$scoreboard[] = TextFormat::colorize("&6&l  ");
				$scoreboard[] = TextFormat::colorize("&6&lTEAM: &r&e") . $player->getFocus()->getFaction()->getName();
				$scoreboard[] = TextFormat::colorize("&c- &eHQ: &r&e") . $player->getFocus()->getFaction()->getHomeString();
				$scoreboard[] = TextFormat::colorize("&c- &eDTR: &r&e") . $player->getFocus()->getFaction()->getDTRColored();
				$scoreboard[] = TextFormat::colorize("&c- &eOnline: &r&e") . count($player->getFocus()->getFaction()->getOnlineMembers());
			}
		}
        if($player->getFaction() !== null && $player->getFaction()->getRally() instanceof FactionRally) {
            $scoreboard[] = TextFormat::colorize("&e&l  ");
            $scoreboard[] = TextFormat::colorize("&6&lRALLY: &r&e");
            $scoreboard[] = TextFormat::colorize("&c- &ePLAYER: &r&e") . $player->getFaction()->getRally()->getWho();
            $scoreboard[] = TextFormat::colorize("&c- &eDTR: &r&e") . $player->getFaction()->getRally()->getPositionString();
        }
        if(count($scoreboard) >= 1){
            $scoreboard[] = TextFormat::GRAY . " ";
            $texting = [TextFormat::GRAY . TextFormat::GRAY . TextFormat::BOLD . " "];
      	  $scoreboard = array_merge($texting, $scoreboard);
        }else{
        	$api->remove($player);
        	return;
        }
        $api->newScoreboard($player, $player->getName(), TextFormat::GOLD . "PacmanLife | HCF");
        if($api->getObjectiveName($player) !== null){
            foreach($scoreboard as $line => $key){
                $api->remove($player);
                $api->newScoreboard($player, $player->getName(), TextFormat::GOLD . "PacmanLife | HCF");
            }
        }
        foreach($scoreboard as $line => $key){
            $api->setLine($player, $line + 1, $key);
        }
    }
}
