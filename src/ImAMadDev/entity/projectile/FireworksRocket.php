<?php

namespace ImAMadDev\entity\projectile;

use ImAMadDev\entity\projectile\animation\FireworkParticleAnimation;
use ImAMadDev\item\Fireworks;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class FireworksRocket extends Entity
{

    public const DATA_FIREWORK_ITEM = 16; //firework item

    public static function getNetworkTypeId(): string
    {
        return EntityIds::FIREWORKS_ROCKET;
    }

    /** @var int */
    protected int $lifeTime = 0;
    /** @var Fireworks */
    protected Fireworks $fireworks;

    public function __construct(Location $location, Fireworks $fireworks, ?int $lifeTime = null)
    {
        $this->fireworks = $fireworks;
        parent::__construct($location, $fireworks->getNamedTag());
        $this->setMotion(new Vector3(0.001, 0.05, 0.001));

        if ($fireworks->getNamedTag()->getCompoundTag("Fireworks") !== null) {
            $this->setLifeTime($lifeTime ?? $fireworks->getRandomizedFlightDuration());
        }

        $location->getWorld()->broadcastPacketToViewers($this->location, LevelSoundEventPacket::create(LevelSoundEvent::LAUNCH, $this->location->asVector3(), -1, self::getNetworkTypeId(), false, false));
    }

    protected function tryChangeMovement(): void
    {
        $this->motion->x *= 1.15;
        $this->motion->y += 0.04;
        $this->motion->z *= 1.15;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->closed) {
            return false;
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);
        if ($this->doLifeTimeTick()) {
            $hasUpdate = true;
        }

        return $hasUpdate;
    }

    public function setLifeTime(int $life): void
    {
        $this->lifeTime = $life;
    }

    protected function doLifeTimeTick(): bool
    {
        if (--$this->lifeTime < 0 && !$this->isFlaggedForDespawn()) {
            $this->doExplosionAnimation();
            $this->playSounds();
            $this->flagForDespawn();
            return true;
        }

        return false;
    }

    protected function doExplosionAnimation(): void
    {
        $this->broadcastAnimation(new FireworkParticleAnimation($this), $this->getViewers());
    }

    public function playSounds(): void
    {
        // This late in, there's 0 chance fireworks tag is null
        $fireworksTag = $this->fireworks->getNamedTag()->getCompoundTag("Fireworks");
        $explosionsTag = $fireworksTag->getListTag("Explosions");
        if ($explosionsTag === null) {
            // We don't throw an error here since there are fireworks that can die without noise or particles,
            // which means they are lacking an explosion tag.
            return;
        }

        foreach ($explosionsTag->getValue() as $info) {
            if ($info instanceof CompoundTag) {
                if ($info->getByte("FireworkType", 0) === Fireworks::TYPE_HUGE_SPHERE) {
                    $this->getWorld()->broadcastPacketToViewers($this->location, LevelSoundEventPacket::create(LevelSoundEvent::LARGE_BLAST, $this->location->asVector3(), -1, self::getNetworkTypeId(), false, false));
                } else {
                    $this->getWorld()->broadcastPacketToViewers($this->location, LevelSoundEventPacket::create(LevelSoundEvent::BLAST, $this->location->asVector3(), -1, self::getNetworkTypeId(), false, false));
                }

                if ($info->getByte("FireworkFlicker", 0) === 1) {
                    $this->getWorld()->broadcastPacketToViewers($this->location, LevelSoundEventPacket::create(LevelSoundEvent::TWINKLE, $this->location->asVector3(), -1, self::getNetworkTypeId(), false, false));
                }
            }
        }
    }

    public function syncNetworkData(EntityMetadataCollection $properties): void
    {
        parent::syncNetworkData($properties);
        $properties->setCompoundTag(self::DATA_FIREWORK_ITEM, $this->fireworks->getNamedTag());
    }

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.25, 0.25);
    }
}