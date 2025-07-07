<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Segment\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Segment\SegmentPartId;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class SegmentPartIdDoctrineType.
 */
class SegmentPartIdDoctrineType extends UuidType
{
    const NAME = 'segment_part_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof SegmentPartId) {
            return $value;
        }

        return new SegmentPartId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null == $value) {
            return null;
        }

        if ($value instanceof SegmentPartId) {
            return $value->__toString();
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
