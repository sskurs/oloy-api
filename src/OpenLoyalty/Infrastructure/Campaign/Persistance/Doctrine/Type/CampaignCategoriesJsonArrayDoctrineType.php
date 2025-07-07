<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CampaignCategoriesJsonArrayDoctrineType extends Type
{
    const NAME = 'campaign_categories_json_array';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return [];
        }
        $value = (is_resource($value)) ? stream_get_contents($value) : $value;
        $decoded = json_decode($value, true);
        return $decoded ?: [];
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return self::NAME;
    }
} 