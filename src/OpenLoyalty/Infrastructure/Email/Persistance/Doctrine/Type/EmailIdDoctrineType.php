<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Email\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Email\EmailId;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class EmailIdDoctrineType.
 */
class EmailIdDoctrineType extends UuidType
{
    const NAME = 'email_id';

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof EmailId) {
            return $value;
        }

        return new EmailId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof EmailId) {
            return $value->__toString();
        }

        if (!empty($value)) {
            return (string)$value;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
