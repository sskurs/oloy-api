<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityController.
 */
class SecurityController extends AbstractFOSRestController
{
    /**
     * This method can be used to log out current user. It will revoke all refresh tokens assigned to current user so it will not be possible
     * to obtain new token based on stored refresh token.
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.security.revoke_refresh_token", path="/token/revoke")
     *
     * @Method("GET")
     * @Security("is_granted('REVOKE_REFRESH_TOKEN')")
     */
    public function revokeRefreshTokenAction()
    {
        // find all tokens by logged user
        /** @var UserInterface $user */
        $user = $this->getUser();
        $tokenManager = $this->get('gesdinet.jwtrefreshtoken.refresh_token_manager');
        $tokenRepository = $this->getDoctrine()->getRepository('GesdinetJWTRefreshTokenBundle:RefreshToken');
        $tokens = $tokenRepository->findBy(['username' => $user->getUsername()]);
        foreach ($tokens as $token) {
            $tokenManager->delete($token);
        }

        return $this->view([], 200);
    }

    /**
     * Get the currently authenticated user details.
     *
     * @Route(name="oloy.security.me", path="/security/me")
     * @Method("GET")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function meAction()
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->view(['error' => 'Not authenticated'], 401);
        }

        // Return user details based on type
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'status' => $user->isEnabled() ? 'active' : 'inactive',
            'createdAt' => $user->getCreatedAt() ? $user->getCreatedAt()->format('c') : null,
            'lastLoginAt' => $user->getLastLogin() ? $user->getLastLogin()->format('c') : null,
        ];

        // Add type-specific fields
        if ($user instanceof \OpenLoyalty\Bundle\UserBundle\Entity\Customer) {
            $userData['firstName'] = $user->getFirstName();
            $userData['lastName'] = $user->getLastName();
            $userData['phone'] = $user->getPhone();
        } elseif ($user instanceof \OpenLoyalty\Bundle\UserBundle\Entity\Seller) {
            $userData['firstName'] = $user->getFirstName();
            $userData['lastName'] = $user->getLastName();
            $userData['phone'] = $user->getPhone();
        } elseif ($user instanceof \OpenLoyalty\Bundle\UserBundle\Entity\Admin) {
            $userData['firstName'] = $user->getFirstName();
            $userData['lastName'] = $user->getLastName();
        }

        return $this->view($userData, 200);
    }
}
