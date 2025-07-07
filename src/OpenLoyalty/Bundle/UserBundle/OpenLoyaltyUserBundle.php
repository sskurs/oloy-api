<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle;

use OpenLoyalty\Bundle\UserBundle\DependencyInjection\Compiler\RemoveGuardAuthenticatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenLoyaltyUserBundle.
 */
class OpenLoyaltyUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveGuardAuthenticatorPass());
    }
}
