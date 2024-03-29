<?php

namespace ImAMadDev\customenchants;

use ImAMadDev\customenchants\types\{Gappler,
    LifeSteal,
    Implants,
    Glow,
    Nutrition,
    Smelting,
    Overload,
    BurnShield,
    Fortune,
    Speed,
    HellForget,
    Invisibility,
    Strength,
    JumpBoost,
    Unrepairable};
use ImAMadDev\HCF;
use ImAMadDev\utils\InventoryUtils;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{EnchantmentInstance,
    Enchantment};
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\utils\TextFormat;

class CustomEnchantments {

	/** @var array[] */
	protected static array $enchantments = [];

    /**
     * @return void
     */
    public static function init() : void {
       self::setup();
    }

    protected static function setup(): void
    {
        self::register(new Speed());
        self::register(new BurnShield());
        self::register(new Invisibility());
        self::register(new JumpBoost());
        self::register(new Strength());
        self::register(new Glow());
        self::register(new HellForget());
        self::register(new LifeSteal());
        self::register(new Nutrition());
        self::register(new Implants());
        self::register(new Fortune());
        self::register(new Smelting());
        self::register(new Overload());
        self::register(new Gappler());
        self::register(new Unrepairable());
        HCF::getInstance()->getLogger()->info("§aThe Custom enchantments have been loaded! Number of enchants: " . count(self::$enchantments));
    }

    public static function register(CustomEnchantment $enchant): void
    {
        EnchantmentIdMap::getInstance()->register($enchant->getId(), $enchant);
        self::$enchantments[$enchant->getName()] = $enchant;
        HCF::getInstance()->getLogger()->debug("Custom Enchantment '" . $enchant->getName() . "' registered with id " . $enchant->getId());
    }
    
    public static function getEnchantedBook(string $enchantName, int $level = 1) : ? Item {
    	$item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
 	    $item->getNamedTag()->setTag(InventoryUtils::CUSTOM_ENCHANTMENT, clone CompoundTag::create());
		$enchantment = CustomEnchantments::getEnchantmentByName($enchantName);
		$item->addEnchantment(new EnchantmentInstance($enchantment, $level));
		$newLore = $item->getLore();
		array_unshift($newLore, $enchantment->getNameWithFormat($level));
		$item->setLore($newLore);
		return $item;
    }

    public static function displayEnchants(ItemStack $itemStack): ItemStack
    {
        $item = TypeConverter::getInstance()->netItemStackToCore($itemStack);
        if (count($item->getEnchantments()) > 0) {
            $additionalInformation = $item->hasCustomName() ? explode("\n", $item->getCustomName())[0] : $item->getName();
            foreach ($item->getEnchantments() as $enchantmentInstance) {
                $enchantment = $enchantmentInstance->getType();
                if ($enchantment instanceof CustomEnchantment) {
                    $additionalInformation .= "\n" . TextFormat::RESET . TextFormat::AQUA . $enchantment->getNameWithFormat($enchantmentInstance->getLevel());
                }
            }
            if ($item->getNamedTag()->getTag(Item::TAG_DISPLAY)) $item->getNamedTag()->setTag("OriginalDisplayTag", $item->getNamedTag()->getTag(Item::TAG_DISPLAY)->safeClone());
                $item = $item->setCustomName($additionalInformation);
            }
        return TypeConverter::getInstance()->coreItemStackToNet($item);
    }


    public static function getEnchantmentByName(string $name) : ? Enchantment {
    	return self::$enchantments[$name] ?? null;
    }
    
    public static function getEnchantments() : array {
    	return self::$enchantments;
    }

}