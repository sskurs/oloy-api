<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * Class PhotoIdDoctrineType.
 */
class PhotoIdDoctrineType extends StringType
{
    const NAME = 'photo_id';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        return (string) $value;
    }

    public function getName()
    {
        return self::NAME;
    }
} 