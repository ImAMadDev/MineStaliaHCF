<?php

namespace ImAMadDev\crate\types;

use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\nbt\tag\StringTag;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\crate\Crate;
use ImAMadDev\manager\AbilityManager;
use ImAMadDev\customenchants\Enchantments;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

use function count;

class Anubis extends Crate {

	/** @var string */
	private string $name = 'Anubis';

	/** @var Item[] */
	private array $contents = [];
	
	public const ANUBIS_KEY = "AnubisKey";

	/**
	 * @return array
	 */
//x3 Ability Random (Ninja Shear | StormBreaker | Fuerza Portable)
	public function getInventory(): array {
		$items = [];
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$this->addCustomEnchantment($helmet, 'Auto_Repair', 2);
		$helmet->setCustomName(TextFormat::DARK_BLUE . "Anubis Helmet");
		$items[11] = $helmet;
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::KOTH_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$this->addCustomEnchantment($chestplate, 'Auto_Repair', 2);
		$this->addCustomEnchantment($chestplate, 'Burn_Shield', 2);
		$chestplate->setCustomName(TextFormat::DARK_BLUE . "Anubis Chestplate");
		$items[12] = $chestplate;
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$this->addCustomEnchantment($leggings, 'Auto_Repair', 2);
		$this->addCustomEnchantment($leggings, 'Implants', 2);
		$leggings->setCustomName(TextFormat::DARK_BLUE . "Anubis Leggings");
		$items[13] = $leggings;
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 10));
		$this->addCustomEnchantment($boots, 'Speed', 2);
		$this->addCustomEnchantment($boots, 'Auto_Repair', 2);
		$boots->setCustomName(TextFormat::DARK_BLUE . "Anubis Boots");
		$items[14] = $boots;
		
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::FREE_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$this->addCustomEnchantment($sword, 'Nutrition', 3);
		$sword->setCustomName(TextFormat::DARK_BLUE . "Anubis Sword");
		$items[10] = $sword;
		
		$sword2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::PAID_SHARPNESS));
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$this->addCustomEnchantment($sword2, 'Nutrition', 2);
		$sword2->setCustomName(TextFormat::DARK_BLUE . "Anubis Sword");
		$items[16] = $sword2;
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 5));
		$bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FLAME), 2));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 2));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
		$bow->setCustomName(TextFormat::DARK_BLUE . 'Anubis Bow');
		$items[15] = $bow;
		
		$gold_block = ItemFactory::getInstance()->get(ItemIds::GOLD_BLOCK, 0, 64);
		$iron_block = ItemFactory::getInstance()->get(ItemIds::IRON_BLOCK, 0, 64);
		$emerald_block = ItemFactory::getInstance()->get(ItemIds::EMERALD_BLOCK, 0, 64);
		$diamond_block = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BLOCK, 0, 64);
		
		$items[2] = $gold_block;
		
		$items[3] = $iron_block;
		
		$items[6] = $diamond_block;
		$items[5] = $emerald_block;
		
		$items[21] = AbilityManager::getInstance()->getAbilityByName('Strength_Portable')->get(3);
		
		$items[22] = AbilityManager::getInstance()->getAbilityByName('Storm_Breaker')->get(3);
		
		$items[20] = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 10);
		
		$items[24] = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 64);
		
		$items[4] = ItemFactory::getInstance()->get(ItemIds::COBWEB, 0, 32);
		
		return $items;
	}
	
	public function addCustomEnchantment(Item $item, string $name, int $level) : Item {
		$enchantment = Enchantments::getEnchantmentByName($name);
		$item->addEnchantment(new EnchantmentInstance($enchantment, $level));
		$newLore = $item->getLore();
		array_unshift($newLore, $enchantment->getNameWithFormat($level));
		$item->setLore($newLore);
		return $item;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&9ANUBIS CRATE");
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getWorld()->getBlockIdAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ()) == Block::GOLD_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === Item::DYE && $item->getDamage() === 5 && $item->getNamedTagEntry(self::ANUBIS_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 5, $count);
		$item->setNamedTagEntry(new CompoundTag(self::KEY_TAG));
		$item->setNamedTagEntry(new CompoundTag(self::ANUBIS_KEY));
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::DARK_BLUE . "Anubis Crate", TextFormat::BOLD . TextFormat::DARK_BLUE . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
		$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		return $item;
	}
	
	public function getContents(HCFPlayer $player) : void {
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName($this->getColoredName() . " " . TextFormat::GREEN . "Crate Content");
		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
			return $transaction->discard();
		});
		$menu->send($player);
		foreach($this->getInventory() as $slot => $item) {
			$menu->getInventory()->setItem($slot, $item);
		}
	} 

	public function open(HCFPlayer $player, Block $block) : void {
		$status = $player->getInventoryStatus(1);
		if($status === "FULL") {
			$player->sendBack($block->asVector3(), 1);
			return;
		} else {
			$items = [];
			foreach($this->getInventory() as $slot => $item) {
				$items[] = $item;
			}
			$win = $items[array_rand($items)];
			$name = $win->hasCustomName() === true ? $win->getCustomName() : $win->getName();
			$player->getInventory()->addItem($win);
			$item = $player->getInventory()->getItemInHand();
			$item->setCount($item->getCount() - 1);
			$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::DARK_BLUE . TextFormat::BOLD . $name);
		}
	}

}