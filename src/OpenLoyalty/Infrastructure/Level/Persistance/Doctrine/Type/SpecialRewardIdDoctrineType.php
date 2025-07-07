<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Level\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Level\SpecialRewardId;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class SpecialRewardIdDoctrineType.
 */
final class SpecialRewardIdDoctrineType extends UuidType
{
    const NAME = 'special_reward_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof SpecialRewardId) {
            return $value;
        }

        return new SpecialRewardId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null == $value) {
            return null;
        }

        if ($value instanceof SpecialRewardId) {
            return $value->__toString();
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
