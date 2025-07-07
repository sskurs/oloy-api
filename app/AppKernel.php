<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
                
            new FOS\RestBundle\FOSRestBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new \OpenLoyalty\Bundle\UserBundle\OpenLoyaltyUserBundle(),
            new OpenLoyalty\Bundle\LevelBundle\OpenLoyaltyLevelBundle(),
            new OpenLoyalty\Bundle\PointsBundle\OpenLoyaltyPointsBundle(),
            new OpenLoyalty\Bundle\SettingsBundle\OpenLoyaltySettingsBundle(),
            new OpenLoyalty\Bundle\TransactionBundle\OpenLoyaltyTransactionBundle(),
            new OpenLoyalty\Bundle\EarningRuleBundle\OpenLoyaltyEarningRuleBundle(),
            new OpenLoyalty\Bundle\PosBundle\OpenLoyaltyPosBundle(),
            new OpenLoyalty\Bundle\SegmentBundle\OpenLoyaltySegmentBundle(),
            new OpenLoyalty\Bundle\EmailBundle\OpenLoyaltyEmailBundle(),
            new OpenLoyalty\Bundle\PaginationBundle\OpenLoyaltyPaginationBundle(),
            new OpenLoyalty\Bundle\CampaignBundle\OpenLoyaltyCampaignBundle(),
            new OpenLoyalty\Bundle\AnalyticsBundle\OpenLoyaltyAnalyticsBundle(),
            new OpenLoyalty\Bundle\UtilityBundle\OpenLoyaltyUtilityBundle(),
            new \OpenLoyalty\Bundle\PluginBundle\OpenLoyaltyPluginBundle(),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new OpenLoyalty\Bundle\AuditBundle\OpenLoyaltyAuditBundle(),
            new OpenLoyalty\Bundle\EmailSettingsBundle\OpenLoyaltyEmailSettingsBundle(),
            new OpenLoyalty\Bundle\DemoBundle\OpenLoyaltyDemoBundle(),
        ];
        $searchPath = dirname(__DIR__).'/src/OpenLoyaltyPlugin';
        $finder     = new \Symfony\Component\Finder\Finder();
        $finder->files()
            ->followLinks()
            ->depth('1')
            ->in($searchPath)
            ->name('*Bundle.php');
        foreach ($finder as $file) {
            $dirname  = basename($file->getRelativePath());
            $filename = substr($file->getFilename(), 0, -4);

            $class = '\\OpenLoyaltyPlugin'.'\\'.$dirname.'\\'.$filename;

            if (class_exists($class)) {
                $plugin = new $class();
                if ($plugin instanceof \Symfony\Component\HttpKernel\Bundle\Bundle) {
                    $bundles[] = $plugin;
                }
                unset($plugin);
            }
        }

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Symfony\Bundle\MakerBundle\MakerBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
