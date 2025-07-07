<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SettingsBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use OpenLoyalty\Bundle\SettingsBundle\Model\Settings;

/**
 * Class DoctrineSettingsManager.
 */
class DoctrineSettingsManager implements SettingsManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * SettingsManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Settings $settings, $flush = true)
    {
        foreach ($settings->getEntries() as $entry) {
            $this->em->persist($entry);
        }

        if ($flush) {
            $this->em->flush();
        }
    }

    public function getSettings()
    {
        $entries = $this->em->getRepository('OpenLoyaltySettingsBundle:SettingsEntry')->findAll();
        if ($entries instanceof ArrayCollection) {
            $entries = $entries->toArray();
        }

        return Settings::fromArray($entries);
    }

    public function getSettingByKey($key)
    {
        return $this->em->getRepository('OpenLoyaltySettingsBundle:SettingsEntry')
            ->findOneBy(['key' => $key]);
    }
}
