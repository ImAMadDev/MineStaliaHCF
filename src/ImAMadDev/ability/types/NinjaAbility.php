<?php

namespace ImAMadDev\ability\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\entity\effect\EffectInstance;

use ImAMadDev\HCF;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\ability\Ability;
use ImAMadDev\ability\ticks\NinjaAbilityTick;

class NinjaAbility extends Ability {

	/** @var string */
	private string $name = 'Ninja_Ability';

	private string $description = "&eWhen you use it you will be transported to the last player who hit you.\n&cHas a cooldown of 4 minutes";
	
	private int $cooldown = 240;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::NETHERSTAR, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([TextFormat::colorize($this->description)]);
		return $item;
	}
	
	public function consume(HCFPlayer $player) : void {
		$time = (120 - (time() - PlayerData::getCountdown($player->getName(), $this->name)));
		if($time > 0) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $time));
			return;
		}
		$tagger = HCF::getInstance()->getCombatManager()->getTagDamager($player);
		if($tagger === null) {
			$player->sendMessage(TextFormat::RED . "No one has tagged you!");
			return;
		}
		if(($taggerPlayer = HCF::getInstance()->getServer()->getPlayerByPrefix($tagger)) instanceof HCFPlayer) {
			if(round($taggerPlayer->getPosition()->distance($player->getDirectionVector())) > 30) {
				$player->sendMessage(TextFormat::RED . "You're too far from the player who hits you last!");
			} else {
				PlayerData::setCountdown($player->getName(), $this->name, (time() + 120));
				$player->teleport($taggerPlayer->getPosition());
				$player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), (20 * 4), 1, true));
				$item = $player->getInventory()->getItemInHand();
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
				$taggerPlayer->sendMessage(TextFormat::RED . $player->getName() . TextFormat::YELLOW . " has teleported to your location using " . $this->getColoredName());
				$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&9Ninja Ability&r");
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::NETHERSTAR && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	#[Pure] public function isAbilityName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function obtain(HCFPlayer $player, int $count) : void {
		$status = $player->getInventoryStatus(1);
		if($status === "FULL") {
			$player->getWorld()->dropItem($player->getPosition()->asVector3(), $this->get($count), new Vector3(0, 0, 0));
        } else {
			$player->getInventory()->addItem($this->get($count));
        }
        $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GREEN . TextFormat::BOLD . $this->getColoredName());
    }

}