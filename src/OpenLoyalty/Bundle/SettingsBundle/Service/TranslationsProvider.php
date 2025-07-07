<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SettingsBundle\Service;

use OpenLoyalty\Bundle\SettingsBundle\Model\TranslationsEntry;

interface TranslationsProvider
{
    /**
     * @return TranslationsEntry
     */
    public function getCurrentTranslations();

    public function getTranslationsByKey($key);

    public function getAvailableTranslationsList();

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasTranslation($key);

    public function save(TranslationsEntry $entry);
}
