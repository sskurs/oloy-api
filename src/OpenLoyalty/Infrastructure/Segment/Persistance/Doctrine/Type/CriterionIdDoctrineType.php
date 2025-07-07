<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Segment\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Segment\CriterionId;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class CriterionIdDoctrineType.
 */
class CriterionIdDoctrineType extends UuidType
{
    const NAME = 'criterion_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof CriterionId) {
            return $value;
        }

        return new CriterionId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null == $value) {
            return null;
        }

        if ($value instanceof CriterionId) {
            return $value->__toString();
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
