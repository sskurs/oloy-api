<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class OpenLoyaltyUserExtension.
 */
class OpenLoyaltyUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('oloy.user.customerSearchMaxResults', $config['customer_search_max_results']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        // $loader->load('domain.yml'); // Disabled due to Broadway dependencies
        $loader->load('services.yml');
        $loader->load('voters.yml');

        // Remove the deprecated guard authenticator service if it exists
        if ($container->hasDefinition('lexik_jwt_authentication.security.guard.jwt_token_authenticator')) {
            $container->removeDefinition('lexik_jwt_authentication.security.guard.jwt_token_authenticator');
        }
        if ($container->hasAlias('lexik_jwt_authentication.jwt_token_authenticator')) {
            $container->removeAlias('lexik_jwt_authentication.jwt_token_authenticator');
        }
    }
}
