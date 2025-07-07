<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Level\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OpenLoyalty\Domain\Level\LevelId;

/**
 * Class LevelIdDoctrineType.
 */
final class LevelIdDoctrineType extends Type
{
    const NAME = 'level_id';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof LevelId) {
            return $value;
        }

        return new LevelId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof LevelId) {
            return $value->__toString();
        }

        return $value;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL([
            'length' => 36,
            'fixed' => true,
        ]);
    }
}
