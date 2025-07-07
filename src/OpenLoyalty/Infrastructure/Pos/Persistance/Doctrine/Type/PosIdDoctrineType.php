<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Pos\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Pos\PosId;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class PosIdDoctrineType.
 */
class PosIdDoctrineType extends UuidType
{
    const NAME = 'pos_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof PosId) {
            return $value;
        }

        return new PosId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null == $value) {
            return null;
        }

        if ($value instanceof PosId) {
            return $value->__toString();
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
