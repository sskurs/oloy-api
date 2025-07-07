<?php

namespace OpenLoyalty\Bundle\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Remove deprecated guard authenticator services from LexikJWTAuthenticationBundle.
 */
class RemoveGuardAuthenticatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Remove the deprecated guard authenticator services
        $servicesToRemove = [
            'lexik_jwt_authentication.security.guard.jwt_token_authenticator',
            'lexik_jwt_authentication.jwt_token_authenticator',
        ];

        foreach ($servicesToRemove as $serviceId) {
            if ($container->hasDefinition($serviceId)) {
                $container->removeDefinition($serviceId);
            }
            if ($container->hasAlias($serviceId)) {
                $container->removeAlias($serviceId);
            }
        }
    }
} 