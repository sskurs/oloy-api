<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\EarningRule\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;
use OpenLoyalty\Domain\EarningRule\EarningRuleUsageSubjectId;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class EarningRuleUsageSubjectDoctrineType.
 */
class EarningRuleUsageSubjectDoctrineType extends UuidType
{
    const NAME = 'earning_rule_usage_subject';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof UsageSubject) {
            return $value;
        }

        return new UsageSubject($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null == $value) {
            return null;
        }

        if ($value instanceof UsageSubject) {
            return $value->__toString();
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
